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
            <p>Are you sure you want to delete this item?</p>
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
      <div class="p-2 mb-2 d-flex flex-row  justify-content-between gap-2">
         

          <div class="mb-2">
            <label class="form-label">Section Title</label>
            <input type="text" v-model="section.title" class="form-control" />
          </div>

          <div class="mb-2">
            <label class="form-label">Order</label>
            <input type="number" v-model.number="section.order" class="form-control" />
          </div>
          <div class="align-content-center btn-contanier">
            <button type="button" class="btn btn-sm btn-primary align-self-end me-2" @click="duplicateSection(sectionIndex)">⧉</button>

            <button type="button" class="btn btn-danger btn-sm align-self-end" @click="confirmDelete('section', sectionIndex)">
              &times;
            </button>
          </div>
          
        </div>

        <div class="row-list">
          <label class="form-label">Rows</label>
          <div
            v-for="(row, rowIndex) in section.rows"
            :key="row.id ?? rowIndex"
            class="border p-2 mb-2 d-flex flex-row gap-2"
          >
            <input type="text" v-model="row.row_number" class="form-control" placeholder="Row number" />
            <input type="text" v-model="row.instructions" class="form-control" placeholder="Instructions" />
            <input type="number" v-model.number="row.stitch_number" class="form-control" placeholder="Stitch number" />
            <input type="text" v-model="row.comment" class="form-control" placeholder="Comment" />

            <!-- Duplicate Row Button -->
            <button type="button" class="btn btn-sm btn-outline-primary align-self-end" @click="duplicateRow(sectionIndex, rowIndex)">⧉</button>

            <button type="button" class="btn btn-sm btn-outline-danger align-self-end" @click="confirmDelete('row', sectionIndex, rowIndex)">
              &times;
            </button>
          </div>
        </div>

        <button type="button" class="btn btn-secondary mt-2" @click="addRow(sectionIndex)">Add Row</button>
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
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { Modal } from 'bootstrap';

export default {
  props: ['initialSections', 'initialTitle', 'initialYarnDescription', 'initialToolsDescription', 'updateUrl'],
  data() {
    return {
      isSaving: false,
      deleteTarget: null,
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
      error: null,
      modalInstance: null,
    };
  },
  mounted() {
    this.modalInstance = new Modal(this.$refs.deleteModal);
  },
  methods: {
    addSection() {
      this.pattern.sections.push({ title: '', order: 0, rows: [] });
    },
    addRow(sectionIndex) {
      const rows   = this.pattern.sections[sectionIndex].rows;
      const last   = rows[rows.length - 1] || null;
      let nextNum  = '';           // default when we cannot auto‑increment

      if (last) {
        // Try to read the previous row_number as an integer
        const parsed = parseInt(last.row_number, 10);

        // Only increment if the *entire* value is a number (e.g. "5", "12")
        if (!isNaN(parsed) && String(parsed) === String(last.row_number)) {
          nextNum = parsed + 1;
        }
      }

      rows.push({
        row_number   : nextNum,     // ''  or incremented numeric value
        instructions : '',
        stitch_number: null,
        comment      : ''
      });
    },
    duplicateRow(sectionIndex, rowIndex) {
      const row = this.pattern.sections[sectionIndex].rows[rowIndex];
      const newRow = { ...row};
      this.pattern.sections[sectionIndex].rows.splice(rowIndex + 1, 0, newRow);
    },
    duplicateSection(sectionIndex) {

      const section = this.pattern.sections[sectionIndex];
      const newSection = { ...section};
      this.pattern.sections.splice(sectionIndex + 1, 0, newSection);

      this.pattern.sections[sectionIndex].rows.forEach(row => {
        const newRow = { ...row};
        this.pattern.sections[newSection.sectionIndex].rows.splice(newRow.rowIndex + 1, 0, newRow);
      });
    },
    removeSection(index) {
      this.pattern.sections.splice(index, 1);
    },
    removeRow(sectionIndex, rowIndex) {
      this.pattern.sections[sectionIndex].rows.splice(rowIndex, 1);
    },
    confirmDelete(type, sectionIndex, rowIndex = null) {
      this.deleteTarget = { type, sectionIndex, rowIndex };
      this.modalInstance.show();
    },
    deleteConfirmed() {
      if (!this.deleteTarget) return;
      if (this.deleteTarget.type === 'section') {
        this.removeSection(this.deleteTarget.sectionIndex);
      } else if (this.deleteTarget.type === 'row') {
        this.removeRow(this.deleteTarget.sectionIndex, this.deleteTarget.rowIndex);
      }
      this.modalInstance.hide();
      this.deleteTarget = null;
    },
    submit() {
      this.isSaving = true;
      axios.put(this.updateUrl, this.pattern, {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        }
      })
      .then(() => {
        this.success = 'Pattern updated successfully.';
        this.error = null;
        setTimeout(() => (this.success = null), 3000);
      })
      .catch(error => {
        this.success = null;
        if (error.response && error.response.data) {
          this.error = error.response.data.message || 'An error occurred on the server.';
        } else {
          this.error = 'Network error.';
        }
        setTimeout(() => (this.error = null), 5000);
      })
      .finally(() => (this.isSaving = false));
    }
  }
};
</script>
