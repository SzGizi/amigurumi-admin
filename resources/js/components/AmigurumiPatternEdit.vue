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
      <div ref="sectionContainer">
      <div
        v-for="(section, sectionIndex) in pattern.sections"
        :key="section.id != null ? section.id : 'section-' + sectionIndex"
        class="card mb-3 p-3 section-card"
      >
        <span class="drag-handle cursor-move">⠿</span>
        <input type="hidden" v-model.number="section.order" />
    
        <div class="p-2 mb-2 d-flex flex-row  justify-content-between gap-2">
              <div class="mb-2">
                <label class="form-label">Section Title</label>
                <input type="text" v-model="section.title" class="form-control" required  />
              </div>

              <div class="align-content-center btn-contanier">
                
                <button @click="moveSectionUp(sectionIndex)" :disabled="sectionIndex === 0">⬆️</button>
                <button @click="moveSectionDown(sectionIndex)" :disabled="sectionIndex === pattern.sections.length - 1">⬇️</button>

                <button type="button" class="btn btn-sm btn-primary align-self-end me-2" @click="duplicateSection(sectionIndex)">⧉</button>

                <button type="button" class="btn btn-danger btn-sm align-self-end" @click="confirmDelete('section', sectionIndex)">
                  &times;
                </button>
              </div>
              </div>
            
          <h3 class="">Rows</h3>
          <div class="row-list">
         
            <div
                v-for="(row, rowIndex) in section.rows"
                :key="row.id != null ? row.id : 'row-' + row.order"
                class="border p-2 mb-2 d-flex flex-row gap-2"
              >
              <span class="drag-handle-row" style="cursor: grab; user-select: none; padding: 0 8px;">⠿</span>
              <span>{{ row.order }}</span>
              <input type="hidden" v-model.number="row.order" />
              <input type="text" v-model="row.row_number" class="form-control" placeholder="Row number" required />
              <input type="text" v-model="row.instructions" class="form-control" placeholder="Instructions" required />
              <input type="number" v-model.number="row.stitch_number" class="form-control" placeholder="Stitch number" />
              <input type="text" v-model="row.comment" class="form-control" placeholder="Comment" />


              <button type="button" class="btn btn-sm btn-outline-primary align-self-end" @click="duplicateRow(sectionIndex, rowIndex)">⧉</button>

              <button type="button" class="btn btn-sm btn-outline-danger align-self-end" @click="confirmDelete('row', sectionIndex, rowIndex)">
                &times;
              </button>
  
              <button @click="moveRowUp(sectionIndex, rowIndex)" :disabled="rowIndex === 0">⬆️</button>
              <button @click="moveRowDown(sectionIndex, rowIndex)" :disabled="rowIndex === section.rows.length - 1">⬇️</button>
            </div>
          </div>
       
        <button type="button" class="btn btn-secondary mt-2" @click="addRow(sectionIndex)">Add Row</button>
      </div>
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
import Sortable from 'sortablejs';

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
          rows: (section.rows ?? []).map(row => ({
            id: row.id ?? null,
            row_number: row.row_number ?? '',
            instructions: row.instructions ?? '',
            stitch_number: row.stitch_number ?? null,
            comment: row.comment ?? '',
            order: row.order ?? 0,
          }))
        }))
      },
      success: null,
      error: null,
      modalInstance: null,
    };
  },
  mounted() {
    this.modalInstance = new Modal(this.$refs.deleteModal);

    this.$nextTick(() => {
      Sortable.create(this.$refs.sectionContainer, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: evt => {
          const moved = this.pattern.sections.splice(evt.oldIndex, 1)[0];
          this.pattern.sections.splice(evt.newIndex, 0, moved);
          this.updateSectionOrders();
        }
      });
      this.initSortableRows();
    });

    
  },
  onEnd: evt => {
  const rows = this.pattern.sections[sectionIndex].rows;
  console.log('Before splice:', rows, 'oldIndex:', evt.oldIndex, 'newIndex:', evt.newIndex);

  const movedRow = rows.splice(evt.oldIndex, 1)[0];

  console.log('Moved row:', movedRow);

  rows.splice(evt.newIndex, 0, movedRow);

  console.log('After splice:', rows);

  this.updateRowOrders(sectionIndex);
},
  methods: {
    addSection() {
      this.pattern.sections.push({ 
        title: '', 
        id: null,
        order: this.pattern.sections.length + 1,
        rows: [] 
      });
    },
    addRow(sectionIndex) {
      const rows = this.pattern.sections[sectionIndex].rows;
      const last = rows[rows.length - 1] || null;
      let nextNum = '';

      if (last) {
        const parsed = parseInt(last.row_number, 10);
        if (!isNaN(parsed) && String(parsed) === String(last.row_number)) {
          nextNum = parsed + 1;
        }
      }

      rows.push({
        row_number: nextNum === '' ? '' : String(nextNum),
        instructions: '',
        stitch_number: null,
        comment: '',
        id: null,
        order: rows.length + 1  // Itt adjunk alapértelmezett order értéket
      });
    },
    duplicateRow(sectionIndex, rowIndex) {
      const row = this.pattern.sections[sectionIndex].rows[rowIndex];
      const newRow = { ...row, row_number: String(row.row_number), order: row.order + 1 };
      this.pattern.sections[sectionIndex].rows.splice(rowIndex + 1, 0, newRow);
      this.updateRowOrders(sectionIndex); // Frissítsd az order-eket utána
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
    moveSectionUp(index) {
      if (index > 0) {
        const sections = this.pattern.sections;
        [sections[index - 1], sections[index]] = [sections[index], sections[index - 1]];
        this.updateSectionOrders();
      }
    },
    moveSectionDown(index) {
      if (index < this.pattern.sections.length - 1) {
        const sections = this.pattern.sections;
        [sections[index + 1], sections[index]] = [sections[index], sections[index + 1]];
        this.updateSectionOrders();
      }
    },
    updateSectionOrders() {
      this.pattern.sections.forEach((section, index) => {
        section.order = index + 1;
      });
    },
    moveRowUp(sectionIndex, rowIndex) {
      if (rowIndex === 0) return;
      const rows = this.pattern.sections[sectionIndex].rows;

      [rows[rowIndex - 1], rows[rowIndex]] = [rows[rowIndex], rows[rowIndex - 1]];

      this.updateRowOrders(sectionIndex);
    },
    moveRowDown(sectionIndex, rowIndex) {
      const rows = this.pattern.sections[sectionIndex].rows;
      if (rowIndex >= rows.length - 1) return;

      [rows[rowIndex + 1], rows[rowIndex]] = [rows[rowIndex], rows[rowIndex + 1]];

      this.updateRowOrders(sectionIndex);
    },
    
    initSortableRows() {
      this.pattern.sections.forEach((section, sectionIndex) => {
        const sectionEl = this.$refs.sectionContainer.querySelectorAll('.section-card')[sectionIndex];
        if (!sectionEl) {
          console.warn(`Section container not found for index ${sectionIndex}`);
          return;
        }
        const rowListContainer = sectionEl.querySelector('.row-list');
        if (!rowListContainer) {
          console.warn(`Row list container not found for section index ${sectionIndex}`);
          return;
        }

        Sortable.create(rowListContainer, {
          handle: '.drag-handle-row',
          animation: 150,
          onEnd: evt => {
            const rows = this.pattern.sections[sectionIndex].rows;
            const movedRow = rows.splice(evt.oldIndex, 1)[0];
            rows.splice(evt.newIndex, 0, movedRow);
            this.updateRowOrders(sectionIndex);
          }
        });
      });
    },
    updateRowOrders(sectionIndex) {
      const rows = this.pattern.sections[sectionIndex].rows;
      if (!rows) return;
      this.pattern.sections[sectionIndex].rows = rows.map((row, idx) => ({
        ...row,
        order: idx + 1
      }));
      console.log("Updated rows orders:", this.pattern.sections[sectionIndex].rows.map(r => r.order));
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
        setTimeout(() => (this.error = null), 50000);
      })
      .finally(() => (this.isSaving = false));
    }
  }
};
</script>
