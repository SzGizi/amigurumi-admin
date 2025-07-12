<template>
  <div class="container py-4">
    <h1 class="mb-4">Edit Amigurumi Pattern</h1>

    <div v-if="success" class="alert alert-success">{{ success }}</div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <form @submit.prevent="submit">
      <!-- Pattern Info -->
      <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" id="title" v-model="pattern.title" class="form-control">
      </div>

      <div class="mb-3">
        <label for="yarn_description" class="form-label">Yarn Description</label>
        <textarea id="yarn_description" v-model="pattern.yarn_description" class="form-control"></textarea>
      </div>

      <div class="mb-3">
        <label for="tools_description" class="form-label">Tools Description</label>
        <textarea id="tools_description" v-model="pattern.tools_description" class="form-control"></textarea>
      </div>

      <!-- Sections and Rows -->
      <h4 class="mt-4">Sections</h4>
      <div v-for="(section, sectionIndex) in pattern.sections" :key="section.id ?? sectionIndex" class="card mb-3 p-3">
        <button type="button" class="btn btn-danger btn-sm float-end" @click="removeSection(sectionIndex)">&times;</button>

        <div class="mb-2">
          <label class="form-label">Section Title</label>
          <input type="text" v-model="section.title" class="form-control">
        </div>

        <div class="mb-2">
          <label class="form-label">Order</label>
          <input type="number" v-model.number="section.order" class="form-control">
        </div>

        <div class="row-list">
          <label class="form-label">Rows</label>
          <div v-for="(row, rowIndex) in section.rows" :key="row.id ?? rowIndex" class="border p-2 mb-2 d-flex">
            <button type="button" class="btn btn-sm btn-outline-danger float-end" @click="removeRow(sectionIndex, rowIndex)">&times;</button>
            <input type="number" v-model.number="row.row_number" class="form-control mb-1" placeholder="Row number">
            <input type="text" v-model="row.instructions" class="form-control" placeholder="Instructions">
            <input type="number" v-model.number="row.stitch_number" class="form-control mb-1" placeholder="Stitch number">
            <input type="text" v-model="row.comment" class="form-control mb-1" placeholder="Comment">
          </div>
        </div>
        <button type="button" class="btn btn-secondary mt-2" @click="addRow(sectionIndex)">Add Row</button>
      </div>

      <button type="button" class="btn btn-outline-primary my-3" @click="addSection">Add Section</button>
      <button type="submit" class="btn btn-primary">Save</button>
    </form>
  </div>
</template>

<script>

export default {
  props: ['initialSections', 'initialTitle', 'initialYarnDescription', 'initialToolsDescription', 'updateUrl'],
  data() {
    return {
      pattern: {
        title: this.initialTitle,
        yarn_description: this.initialYarnDescription,
        tools_description: this.initialToolsDescription,
        sections: this.initialSections.map(section => ({
          ...section,
          rows: section.amigurumiRows || []
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
    removeSection(index) {
      this.pattern.sections.splice(index, 1);
    },
    addRow(sectionIndex) {
      const rows = this.pattern.sections[sectionIndex].rows;
      let newRowNumber = 1;
      if (rows.length > 0 && rows[rows.length - 1].row_number) {
        newRowNumber = rows[rows.length - 1].row_number + 1;
      }
      rows.push({ row_number: newRowNumber, instructions: '', stitch_number: null, comment: '' });
    },
    removeRow(sectionIndex, rowIndex) {
      this.pattern.sections[sectionIndex].rows.splice(rowIndex, 1);
    },
    submit() {
    console.log('Axios submit URL:', this.updateUrl);

    // Direkt fix URL használata
    const url = new URL(this.updateUrl, window.location.origin).href;

    console.log('Using URL:', url);

    axios.put(url, this.pattern, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    })
    .then(response => {
      this.success = 'Pattern updated successfully.';
      this.error = null;
      console.log('Sikeres mentés:', response.data);
    })
    .catch(error => {
      this.success = null;
      if (error.response && error.response.data) {
        this.error = error.response.data.message || 'Valami hiba történt a szerveren.';
      } else {
        this.error = 'Hálózati hiba.';
      }
      console.error('Mentés hiba:', error);
    });
  }
  },
};
</script>
