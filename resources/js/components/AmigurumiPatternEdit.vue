
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

      <div class="modal fade" tabindex="-1" ref="generateRowsModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Generate Rows</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="basic-input">
              <label class="form-label">Operation</label>
              <select class="form-select" v-model="rowGen.operation">
                <option value="inc">Increase</option>
                <option value="dec">Decrease</option>
              </select>
            </div>

            <div class="basic-input">
              <label class="form-label">Row Count</label>
              <input type="number" class="form-control" v-model="rowGen.row_count" placeholder="e.g. 10" />
            </div>
            <div class="basic-input">
              <label class="form-label">Current Stitch Number</label>
              <input type="number" class="form-control" v-model.number="rowGen.stitch_number" placeholder="e.g. 30" />
            </div>

            <div class="basic-input">
              <label class="form-label">Number of Changes (inc/dec)</label>
              <input type="number" class="form-control" v-model.number="rowGen.change_count" placeholder="e.g. 6" />
            </div>

            <div v-if="rowGenError" class="alert alert-danger">{{ rowGenError }}</div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-primary" @click="generateRows">Generate</button>
          </div>
        </div>
      </div>
    </div>

    <form @submit.prevent="submit">
      <div class="row">
       
        <div class="col-md-6">

          <div class="basic-input">
            <label for="title" class="form-label">Title</label>
            <input type="text" id="title" v-model="pattern.title" class="form-control" />
          </div>

          <div class="basic-input">
            <label for="yarn_description" class="form-label">Yarn Description</label>
            <textarea id="yarn_description" v-model="pattern.yarn_description" class="form-control"></textarea>
          </div>

         
        </div>
        <div class="col-md-6 ">
          <div class="basic-input">
            <label for="tools_description" class="form-label">Tools Description</label>
            <textarea  id="tools_description" v-model="pattern.tools_description" class="form-control"></textarea>
          </div>
      
        </div>
         <div class="col-md-12 mb-3">
         
           <FileUploader 
            model-type="AmigurumiPattern" 
            :model-id="pattern.id" 
            ref="fileUploaderRef"
            :pattern-main-image-id="pattern.main_image_id"  
            @updateDeletedImages="onUpdateDeletedImages"
            @updateMainImageId="pattern.main_image_id = $event"
           />
        
        </div>
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

            <div class="d-flex align-items-center flex-row justify-content-between gap-2">
               <div class="arrow-btn-container">
                <span class="drag-handle cursor-move">⠿</span>
                <input type="hidden" v-model.number="section.order" />
                <button  type="button" class="btn" @click="moveSectionUp(sectionIndex)" :disabled="sectionIndex === 0">
                  <i class="bi bi-arrow-up"></i>
                </button>
                <button type="button" class="btn" @click="moveSectionDown(sectionIndex)" :disabled="sectionIndex === pattern.sections.length - 1">
                <i class="bi bi-arrow-down"></i>
                </button>
              </div>
              <div class="basic-input flex-fill mb-0">
                <label class="form-label">Section Title</label>
                <input type="text" v-model="section.title" class="form-control" required />
              </div>

              <div class="funtions-btn-container">
                <button
                    class="btn btn-sm btn-outline-secondary "
                    type="button"
                    @click="toggleCollapse(sectionIndex)"
                  ><i class="bi bi-list-ol"></i></button>
                <button
                  type="button"
                  class="btn btn-sm btn-outline-primary "
                  @click="duplicateSection(sectionIndex)"
                ><i class="bi bi-copy"></i></button>

                <button
                  type="button"
                  class="btn btn-sm btn-outline-danger "
                  @click="confirmDelete('section', sectionIndex)"
                ><i class="bi bi-x"></i></button>
                
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
                      <button  type="button" class="btn" @click="moveRowUp(sectionIndex, rowIndex)" :disabled="rowIndex === 0">
                        <i class="bi bi-arrow-up"></i>
                      </button>
                      <button type="button" class="btn" @click="moveRowDown(sectionIndex, rowIndex)" :disabled="rowIndex === section.rows.length - 1">
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
                      <label class="form-label">St no.</label>
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
              <button type="button" class="btn btn-primary mt-2 ms-2" @click="generateRowsModal(sectionIndex)">
                Generate Rows
              </button>
              <button type="button" class="btn btn-outline-info  mt-2 ms-2" @click="regenerateRowNumbers(sectionIndex)">
                Renumber Rows
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
      <button type="button" class="btn btn-success" @click="downloadPdf">Download PDF</button>
    </form>
  </div>
</template>

<script setup>
  import FileUploader from '@/components/image/FileUploader.vue'
  import { ref } from 'vue'


</script>

<script>
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { Modal } from 'bootstrap';
import draggable from 'vuedraggable';
import { Collapse } from 'bootstrap';


const imageUploaderRef = ref()

export default {
  components: { draggable },
  props: [
    'initialPatternId',
    'initialSections',
    'initialTitle',
    'initialYarnDescription',
    'initialToolsDescription',
    'updateUrl',
    'initialMainImageId'
  ],
  data() {
    return {
      isSaving: false,
      deleteTarget: null,
      pattern: {
        id:this.initialPatternId,
        title: this.initialTitle,
        yarn_description: this.initialYarnDescription,
        tools_description: this.initialToolsDescription,
        images: [],
        deleted_image_ids : [],
        main_image_id : this.initialMainImageId ?? null,
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
      deletemodalInstance: null,
      generateRowmodalInstance: null,
      rowGen: {
        operation: 'inc',       // 'inc' or 'dec'
        row_count: null,        // how many rows to generate
        stitch_number: null,    // starting stitch number
        change_count: null,     // how much to increase/decrease per row
      },
      rowGenError: null,
    };
  },
  mounted() {
    
    this.deletemodalInstance = new Modal(this.$refs.deleteModal);
    this.generateRowmodalInstance = new Modal(this.$refs.generateRowsModal);
   
      axios.get(`/api/patterns/${this.initialPatternId}/images`)
        .then(res => {
          this.pattern.images = res.data; // ! FONTOS: pattern.images-be mentjük
          //console.log('Képek betöltve:', this.pattern.images);
        })
        .catch(error => {
          console.error('Képek betöltése sikertelen:', error);
        });
      
  },
  methods: {
    submit() {
      this.isSaving = true;
      
      this.pattern.deleted_image_ids = this.deletedImageIds != null && this.deletedImageIds.length > 0 ? this.deletedImageIds.join(',') : '';

    
       if (!this.$refs.fileUploaderRef) {
        console.error('ImageUploader ref not found')
        this.isSaving = false
        return
      }
      

      this.$refs.fileUploaderRef.saveModifiedCaptions().then(() => {
        this.$refs.fileUploaderRef.replaceModifiedExistingImages().then(() => {
          this.$refs.fileUploaderRef.uploadPendingImages().then(() => {
            axios
              .put(this.updateUrl , this.pattern, {
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
          });
        });
      });
    },
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
    addRow(sectionIndex, overrideData = {}) {
      const rows = this.pattern.sections[sectionIndex].rows;
      const last = rows[rows.length - 1] || null;
      let nextNum = '';

      if (last) {
        const match = String(last.row_number).match(/(\d+)(?!.*\d)/); // utolsó szám a string végén
        if (match) {
          const number = parseInt(match[1], 10);
          nextNum = String(number + 1);
        }
      }

      const newRow = {
        row_number: nextNum !== '' ? nextNum : '',
        instructions: overrideData.instructions ?? '',
        stitch_number: overrideData.stitch_number ?? null,
        comment: overrideData.comment ?? '',
        id: null,
        uid: crypto.randomUUID(),
        order: rows.length + 1,
        showComment: false,
      };

      rows.push(newRow);
      this.updateRowOrders(sectionIndex);
    },
    generateInstruction(operation, totalStitches, changeCount) {

      if (totalStitches % changeCount !== 0) return null;
      const repeatSize = totalStitches / changeCount;
      const scTotal = repeatSize - (operation === 'inc' ? 1 : 2);

      var before = scTotal;
      var after = 0;

      if(scTotal%2 == 0){
        before = scTotal / 2;
        after = scTotal / 2;
      }

      const beforeSc = before > 0 ? `${before==1?'':before}sc` : '';
      const afterSc = after > 0 ? `${after==1?'':after}sc` : '';
      const change = operation === 'inc' ? 'inc' : 'dec';

      const parts = [beforeSc, change, afterSc].filter(p => p !== '');

      return `*${parts.join(',')}*`;
    },
    generateRows() {
      const { operation, row_count, stitch_number, change_count } = this.rowGen;

      if (!row_count || !stitch_number || !change_count) {
        this.rowGenError = 'Please fill in all fields.';
        return;
      }

      const sectionIndex = this.currentSectionIndex;
      let currentStitches = stitch_number;

      for (let i = 0; i < row_count; i++) {
        
        
        this.addRow(sectionIndex, {
          instructions:this.generateInstruction(operation, currentStitches, change_count),
          stitch_number: currentStitches += operation === 'inc' ? change_count : -change_count,
        });

        
      }

      this.rowGenError = null;
      this.rowGen = {
        operation: 'inc',
        row_count: null,
        stitch_number: null,
        change_count: null,
      };
      this.generateRowmodalInstance.hide();
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
      this.deletemodalInstance.show();
    },
    deleteConfirmed() {
      if (!this.deleteTarget) return;
      if (this.deleteTarget.type === 'section') {
        this.removeSection(this.deleteTarget.sectionIndex);
      } else if (this.deleteTarget.type === 'row') {
        this.removeRow(this.deleteTarget.sectionIndex, this.deleteTarget.rowIndex);
      }
      this.deletemodalInstance.hide();
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
    generateRowsModal(sectionIndex) {
      this.currentSectionIndex = sectionIndex; // Store the current section index
      this.rowGen = { operation: 'inc', row_number: '', stitch_number: null, change_count: null };
      this.rowGenError = null;
      this.generateRowmodalInstance.show();
    },
    regenerateRowNumbers(sectionIndex) {
      const section = this.pattern.sections[sectionIndex];
      if (!section) return;

      const rows = section.rows;
      if (rows.length === 0) return;

      const parseRowNumber = (str) => {
        const trimmed = String(str).trim();
        if (/^\d+$/.test(trimmed)) {
          const value = parseInt(trimmed);
          return { start: value, end: value };
        }

        const match = trimmed.match(/^(\d+)\s*-\s*(\d+)$/);
        if (match) {
          const a = parseInt(match[1]);
          const b = parseInt(match[2]);
          if (b <= a) return null;
          return { start: a, end: b };
        }

        return null;
      };

      // ⛔ Előellenőrzés: hibás értékek kiszűrése (de üres engedélyezett)
      for (let i = 0; i < rows.length; i++) {
        const val = rows[i].row_number;
        if (val === '') continue; // üres sor engedélyezett
        const parsed = parseRowNumber(val);
        if (!parsed) {
          alert(`❌ Error in row ${i + 1}: "${val}" is not a valid format.\nAccepted formats: number or range like "2-5".`);
          return;
        }
      }

      // 🔢 Induló érték beállítása
      let currentStart = 1;
      const firstParsed = parseRowNumber(rows[0].row_number);
      if (firstParsed) {
        currentStart = firstParsed.start;
      }

      // ✅ Újragenerálás
      for (let i = 0; i < rows.length; i++) {
        const parsed = parseRowNumber(rows[i].row_number);
        const delta = parsed ? parsed.end - parsed.start : 0;
        const from = currentStart;
        const to = from + delta;

        rows[i].row_number = delta === 0 ? `${from}` : `${from}-${to}`;
        currentStart = to + 1;
      }
    },
    downloadPdf() {
      this.submit(); // Ensure the pattern is saved before generating PDF
      axios
        .post('/patterns/generate-pdf', this.pattern, {
          responseType: 'blob',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          }
        })
        .then(response => {
          const blob = new Blob([response.data], { type: 'application/pdf' });
          const url = URL.createObjectURL(blob);
          const link = document.createElement('a');
          link.href = url;
          link.download = 'pattern.pdf';
          link.click();
          URL.revokeObjectURL(url);
        })
        .catch(error => {
          alert('❌ PDF generálás sikertelen');
          console.error(error);
        });
    },
    onUpdateDeletedImages(ids) {
      this.deletedImageIds = ids;
    },

  
  },
};
</script>
