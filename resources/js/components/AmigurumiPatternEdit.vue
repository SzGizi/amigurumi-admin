<template>
  <div class="container py-4">
    <h1 class="mb-4">Edit Amigurumi Pattern</h1>

    <div v-if="success" class="alert alert-success">{{ success }}</div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- Confirm Deletion Modal -->
    <div class="modal fade" tabindex="-1" ref="deleteModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete this section?</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-danger" @click="deleteConfirmed">Yes, delete</button>
          </div>
        </div>
      </div>
    </div>

    <form @submit.prevent="submit">
      <!-- Pattern Info -->
      <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" id="title" v-model="pattern.title" class="form-control" />
      </div>

      <div class="mb-3">
        <label for="yarn_description" class="form-label">Yarn Description</label>
        <textarea id="yarn_description" v-model="pattern.yarn_description" class="form-control"></textarea>
      </div>

      <div class="mb-3">
        <label for="tools_description" class="form-label">Tools Description</label>
        <textarea id="tools_description" v-model="pattern.tools_description" class="form-control"></textarea>
      </div>

      <!-- Sections -->
      <h4 class="mt-4">Sections</h4>
      <div
        v-for="(section, sectionIndex) in pattern.sections"
        :key="section.id ?? sectionIndex"
        class="card mb-3 p-3"
      >
        <button type="button" class="btn btn-danger btn-sm float-end" @click="confirmDelete(sectionIndex)">
          &times;
        </button>

        <div class="mb-2">
          <label class="form-label">Section Title</label>
          <input type="text" v-model="section.title" class="form-control" />
        </div>

        <div class="mb-2">
          <label class="form-label">Order</label>
          <input type="number" v-model.number="section.order" class="form-control" />
        </div>

        <div class="row-list">
          <label class="form-label">Rows</label>
          <div
            v-for="(row, rowIndex) in section.rows"
            :key="row.id ?? rowIndex"
            class="border p-2 mb-2 d-flex flex-row gap-2 "
          >
            <input
              type="number"
              v-model.number="row.row_number"
              class="form-control"
              placeholder="Row number"
            />
            <input
              type="text"
              v-model="row.instructions"
              class="form-control"
              placeholder="Instructions"
            />
            <input
              type="number"
              v-model.number="row.stitch_number"
              class="form-control"
              placeholder="Stitch number"
            />
            <input
              type="text"
              v-model="row.comment"
              class="form-control"
              placeholder="Comment"
            />
            <button
              type="button"
              class="btn btn-sm btn-outline-danger align-self-end"
              @click="removeRow(sectionIndex, rowIndex)"
            >
              &times;
            </button>
          </div>
        </div>

        <button type="button" class="btn btn-secondary mt-2" @click="addRow(sectionIndex)">
          Add Row
        </button>
      </div>

      <button type="button" class="btn btn-outline-primary my-3" @click="addSection">Add Section</button>
      <button type="submit" class="btn btn-primary" :disabled="isSaving">
        <span v-if="isSaving" class="spinner-border spinner-border-sm me-1"></span>
        {{ isSaving ? 'Saving...' : 'Save' }}
      </button>
    </form>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
  props: ['initialSections', 'initialTitle', 'initialYarnDescription', 'initialToolsDescription', 'updateUrl'],
  data() {
    return {
      isSaving: false,
      sectionToDeleteIndex: null,
      pattern: {
        title: this.initialTitle,
        yarn_description: this.initialYarnDescription,
        tools_description: this.initialToolsDescription,
        sections: this.initialSections.map(section => ({
          id: section.id,
          title: section.title,
          order: section.order,
          rows: section.rows || []
        }))
      },
      success: null,
      error: null
    };
  },
  methods: {
    addSection() {
      this.pattern.sections.push({ title: '', order: 0, rows: [] });
    },
    confirmDelete(index) {
      this.sectionToDeleteIndex = index;
      const modal = new Modal(this.$refs.deleteModal);
      modal.show();
    },
    deleteConfirmed() {
      this.removeSection(this.sectionToDeleteIndex);
      const modal = Modal.getInstance(this.$refs.deleteModal);
      modal.hide();
    },
    removeSection(index) {
      this.pattern.sections.splice(index, 1);
    },
    addRow(sectionIndex) {
      this.pattern.sections[sectionIndex].rows.push({
        row_number: this.pattern.sections[sectionIndex].rows.length + 1,
        instructions: '',
        stitch_number: null,
        comment: ''
      });
    },
    removeRow(sectionIndex, rowIndex) {
      this.pattern.sections[sectionIndex].rows.splice(rowIndex, 1);
    },
    submit() {
      this.isSaving = true;
      axios.put(this.updateUrl, this.pattern, {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        }
      })
      .then(response => {
        this.success = 'Pattern updated successfully.';
        this.error = null;
        setTimeout(() => this.success = null, 3000);
      })
      .catch(error => {
        this.success = null;
        if (error.response && error.response.data) {
          this.error = error.response.data.message || 'An error occurred on the server.';
        } else {
          this.error = 'Network error.';
        }
        setTimeout(() => this.error = null, 5000);
      })
      .finally(() => {
        this.isSaving = false;
      });
    }
  }
};
</script>
