<template>
  <div class="container py-4">
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
      <div class="basic-input">
        <label for="title" class="form-label">Title</label>
        <input type="text" id="title" v-model="pattern.title" class="form-control" />
      </div>

      <div class="basic-input">
        <label for="yarn_description" class="form-label">Yarn Description</label>
        <textarea id="yarn_description" v-model="pattern.yarn_description" class="form-control"></textarea>
      </div>

      <div class="basic-input">
        <label for="tools_description" class="form-label">Tools Description</label>
        <textarea id="tools_description" v-model="pattern.tools_description" class="form-control"></textarea>
      </div>

      <!-- Sections -->
      <h4 class="mt-4">Sections</h4>
      
      <draggable
        v-model="pattern.sections"
        handle=".drag-handle"
        animation="150"
        @update="updateSectionOrders"
        item-key="uid"
        ref="draggableSections"
      >
        <template #item="{element: section, index: sectionIndex}">
          <div class="card mb-3 p-3 section-card" :key="section.uid">
            

            <div class="p-2 mb-2 d-flex flex-row justify-content-between gap-2">
              <div>
                <span class="drag-handle cursor-move">⠿</span>
                <input type="hidden" v-model.number="section.order" />
              </div>
              <div class="basic-input">
                <label class="form-label">Section Title</label>
                <input type="text" v-model="section.title" class="form-control" required />
              </div>

              <div class="align-content-center btn-contanier">
                <button @click="moveSectionUp(sectionIndex)" :disabled="sectionIndex === 0">⬆️</button>
                <button @click="moveSectionDown(sectionIndex)" :disabled="sectionIndex === pattern.sections.length - 1">⬇️</button>

                <button
                  type="button"
                  class="btn btn-sm btn-primary align-self-end me-2"
                  @click="duplicateSection(sectionIndex)"
                >
                  ⧉
                </button>

                <button
                  type="button"
                  class="btn btn-danger btn-sm align-self-end"
                  @click="confirmDelete('section', sectionIndex)"
                >
                  &times;
                </button>
                <button
                  class="btn btn-sm btn-outline-secondary mb-2"
                  type="button"
                  @click="toggleCollapse(sectionIndex)"
                >
                  Toggle Rows
                </button>
              </div>
              
            </div>

           
            <div class="collapse row-list" :id="'rowsCollapse' + sectionIndex">
              <h3>Rows</h3>
              
              <draggable
                v-model="section.rows"
                :group="'rows' + sectionIndex"
                handle=".drag-handle-row"
                animation="150"
                @update="() => updateRowOrders(sectionIndex)"
                item-key="uid"
                :ref="'draggableRows' + sectionIndex"
                
              >
              

              <template #item="{element: row, index: rowIndex}">
                <div class="border-bottom mb-3" :key="row.uid">
                  <div class="mb-2 d-flex align-items-center flex-row justify-content-between gap-2 ">
                    <input type="hidden" v-model.number="row.order" />
                    <div class="arrow-btn-container">
                      <span class="drag-handle-row">⠿</span>
                      <button class="btn" @click="moveRowUp(sectionIndex, rowIndex)" :disabled="rowIndex === 0">
                        <i class="bi bi-arrow-up"></i>
                      </button>
                      <button class="btn" @click="moveRowDown(sectionIndex, rowIndex)" :disabled="rowIndex === section.rows.length - 1">
                      <i class="bi bi-arrow-down"></i>
                      </button>
                    </div>
                    
                    <div class="basic-input max-w-6 ">    
                      <label class="form-label">Row no.</label>
                      
                      <input type="text"
                      v-model="row.row_number"
                      class="form-control"
                      required
                      />
                    </div>
                  
                    <div class="basic-input flex-fill">    
                      <label class="form-label">Instructions</label>
                      <input
                      type="text"
                      v-model="row.instructions"
                      class="form-control"
                      required
                    />
                    </div>

                    <div class="basic-input max-w-7">    
                      <label class="form-label">St number</label>
                      <input
                        type="number"
                        v-model.number="row.stitch_number"
                        class="form-control"
                      />
                    </div>
                    
                   
                  

                    <div class="funtions-btn-container">
                     <input
                        type="checkbox"
                        class="btn-check"
                        :id="'toggleComment' + sectionIndex + '_' + rowIndex"
                        v-model="row.showComment"
                      />
                      <label
                        class="btn btn-outline-secondary"
                        :class="{ active: row.showComment }"
                        :for="'toggleComment' + sectionIndex + '_' + rowIndex"
                      >
                        <i class="bi bi-chat-right-text"></i>
                      </label>
                      <button
                        type="button"
                        class="btn btn-sm btn-outline-primary align-self-end"
                        @click="duplicateRow(sectionIndex, rowIndex)"
                      ><i class="bi bi-copy"></i></button>

                      <button
                        type="button"
                        class="btn btn-sm btn-outline-danger align-self-end"
                        @click="confirmDelete('row', sectionIndex, rowIndex)"
                      ><i class="bi bi-x"></i></button>
                    </div>
                  </div>
                   <div class="collapse w-100 mt-3 mb-3" :class="{ show: row.showComment }">
                    <div class="basic-input">    
                    <label class="form-label">Comment</label>
                    <input
                      type="text"
                      v-model="row.comment"
                      class="form-control"
                    />
                    </div>
                  </div>
                </div>
              </template>
                
              </draggable>
              <button type="button" class="btn btn-secondary mt-2" @click="addRow(sectionIndex)">
                Add Row
              </button>
            </div>
            
          </div>
        </template>
      </draggable>

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
import draggable from 'vuedraggable';
import { Collapse } from 'bootstrap';

export default {
  components: { draggable },
  props: [
    'initialSections',
    'initialTitle',
    'initialYarnDescription',
    'initialToolsDescription',
    'updateUrl',
  ],
  data() {
    return {
      isSaving: false,
      deleteTarget: null,
      pattern: {
        title: this.initialTitle,
        yarn_description: this.initialYarnDescription,
        tools_description: this.initialToolsDescription,
        sections: this.initialSections.map((section) => ({
          id: section.id,
          title: section.title,
          order: section.order,
          rows: (section.rows ?? []).map((row) => ({
            id: row.id ?? null,
            row_number: row.row_number ?? '',
            instructions: row.instructions ?? '',
            stitch_number: row.stitch_number ?? null,
            comment: row.comment ?? '',
            showComment: !!row.comment,
            order: row.order ?? 0,
            uid: row.uid ?? crypto.randomUUID(),
          })),
          uid: section.uid ?? crypto.randomUUID(),
        })),
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
    updateSectionOrders() {
      this.pattern.sections.forEach((section, index) => {
        if (section) section.order = index + 1;
      });
    },
    updateRowOrders(sectionIndex) {
      const section = this.pattern.sections[sectionIndex];
      if (!section || !Array.isArray(section.rows)) return;
      section.rows.forEach((row, i) => {
        if (row) row.order = i + 1;
      });
    },
    addSection() {
      this.pattern.sections.push({
        title: '',
        id: null,
        uid: crypto.randomUUID(),
        order: this.pattern.sections.length + 1,
        rows: [],
      });
      this.updateSectionOrders();
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
        uid: crypto.randomUUID(),
        order: rows.length + 1,
        showComment: false,
      });
      this.updateRowOrders(sectionIndex);
    },
    duplicateRow(sectionIndex, rowIndex) {
      const row = this.pattern.sections[sectionIndex].rows[rowIndex];
      const newRow = { ...row, row_number: String(row.row_number), order: row.order + 1, uid: crypto.randomUUID() };
      this.pattern.sections[sectionIndex].rows.splice(rowIndex + 1, 0, newRow);
      this.updateRowOrders(sectionIndex);
    },
    duplicateSection(sectionIndex) {
      const section = this.pattern.sections[sectionIndex];
      const newSection = {
        ...section,
        uid: crypto.randomUUID(),
        rows: section.rows.map((row) => ({
          ...row,
          uid: crypto.randomUUID(),
        })),
      };
      this.pattern.sections.splice(sectionIndex + 1, 0, newSection);
      this.updateSectionOrders();
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
      if (!Array.isArray(this.pattern.sections)) return;
      this.pattern.sections.forEach((section, index) => {
        if (section) section.order = index + 1;
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
    toggleCollapse(index) {
      const collapseEl = document.getElementById('rowsCollapse' + index);
      if (collapseEl) {
        const collapseInstance = Collapse.getOrCreateInstance(collapseEl);
        collapseInstance.toggle();
      }
    },
    submit() {
      this.isSaving = true;

      axios
        .put(this.updateUrl, this.pattern, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
          },
        })
        .then(() => {
          this.success = 'Pattern updated successfully.';
          this.error = null;
          setTimeout(() => (this.success = null), 3000);
        })
        .catch((error) => {
          this.success = null;
          if (error.response && error.response.data) {
            this.error = error.response.data.message || 'An error occurred on the server.';
          } else {
            this.error = 'Network error.';
          }
          setTimeout(() => (this.error = null), 50000);
        })
        .finally(() => (this.isSaving = false));
    },
  },
};
</script>
