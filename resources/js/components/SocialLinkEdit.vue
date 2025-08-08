<template>
  <div>
    <h4 class="mt-4">Social Links</h4>

    <draggable
      v-model="socialLinks"
      handle=".drag-handle"
      animation="150"
      @update="updateSocialLinksOrder"
      item-key="id"
      ref="draggableSocialLinks"
    >
      <template #item="{ element: socialLink, index: socialLinkIndex }">
        <div class="card mb-3 p-3 section-card" :key="socialLink.id || socialLink.uid">
          <div class="d-flex align-items-center flex-row justify-content-between gap-2">

            <div class="arrow-btn-container">
              <span class="drag-handle cursor-move">⠿</span>
              <input type="hidden" v-model.number="socialLink.order" />
              <button type="button" class="btn" @click="moveSocialLinkUp(socialLinkIndex)" :disabled="socialLinkIndex === 0">
                <i class="bi bi-arrow-up"></i>
              </button>
              <button type="button" class="btn" @click="moveSocialLinkDown(socialLinkIndex)" :disabled="socialLinkIndex === socialLinks.length - 1">
                <i class="bi bi-arrow-down"></i>
              </button>
            </div>

            <div class="basic-input flex-fill mb-0">
              <label :for="'title-' + socialLinkIndex" class="form-label">Title</label>
              <input
                type="text"
                class="form-control"
                :id="'title-' + socialLinkIndex"
                v-model="socialLink.title"
                placeholder="Title"
              />
            </div>

            <div class="basic-input flex-fill mb-0 ms-3">
              <label :for="'link-' + socialLinkIndex" class="form-label">Link</label>
              <input
                type="text"
                class="form-control"
                :id="'link-' + socialLinkIndex"
                v-model="socialLink.link"
                placeholder="https://example.com"
              />
            </div>

            <div class="funtions-btn-container">
              <button
                class="btn btn-sm btn-outline-info"
                type="button"
                data-bs-toggle="collapse"
                :data-bs-target="'#socialLinkCollapse' + socialLinkIndex"
                aria-expanded="false"
                :aria-controls="'socialLinkCollapse' + socialLinkIndex"
                title="Toggle Details"
              >
                <i class="bi bi-images"></i>
              </button>

              <button
                type="button"
                class="btn btn-sm btn-outline-primary"
                @click="duplicateSocialLink(socialLinkIndex)"
                title="Duplicate Social Link"
              >
                <i class="bi bi-copy"></i>
              </button>

              <button
                type="button"
                class="btn btn-sm btn-outline-danger"
                @click="confirmDelete(socialLinkIndex)"
                title="Delete Social Link"
              >
                <i class="bi bi-x"></i>
              </button>
            </div>
          </div>

          <div class="collapse row-list mt-3" :id="'socialLinkCollapse' + socialLinkIndex">
            <div class="col-md-12 mb-2">
              <label class="form-label">Icon</label>
              <input
                type="file"
                class="form-control"
                accept="image/*"
                @change="handleImageChange($event, socialLinkIndex)"
              />
              <div v-if="socialLink.preview" class="mt-2">
                <img :src="socialLink.preview" alt="Icon preview" class="img-thumbnail" style="max-width: 100px;" />
              </div>
            </div>
          </div>
        </div>
      </template>
    </draggable>

    <button type="button" class="btn btn-outline-primary my-3" @click="addSocialLink">
      Add Social Link
    </button>

    <button type="button" class="btn btn-success" @click="saveSocialLinks">
      Save Changes
    </button>
  </div>
</template>

<script>
import draggable from 'vuedraggable';
import axios from 'axios';

export default {
  components: {
    draggable,
  },
  data() {
    return {
      socialLinks: [],
      deletedSocialLinks: [],
    };
  },
  mounted() {
    this.fetchSocialLinks();
  },

  methods: {
    confirmDelete(index) {
      if (confirm('Are you sure you want to delete this social link?')) {
        const item = this.socialLinks[index];
        if (item.id) {
          // Csak ha már létezik az adatbázisban, akkor töröljük később
          this.deletedSocialLinks.push(item.id);
        }
        this.socialLinks.splice(index, 1); // eltávolítjuk a UI-ból
        this.updateOrders();
      }
    },
    async fetchSocialLinks() {
      try {
        const response = await axios.get('/social-links');
          console.log('Fetched:', response.data);
        this.socialLinks = response.data.map((sl, index) => ({
          ...sl,
          uid: null, // csak új elemekhez kell
          icon: null, // ez lesz az új fájl, ha frissítenék
          preview: sl.icon ? `${window.location.origin}/storage/${sl.icon.replace(/^storage\/?/, '')}` : '',
          order: sl.order ?? index + 1,
        }));
      } catch (error) {
        console.error('Error fetching social links:', error);
        alert('Error loading social links.');
      }
    },

    addSocialLink() {
      const uid = Date.now().toString(36) + Math.random().toString(36).substr(2);
      this.socialLinks.push({
        uid,
        title: '',
        link: '',
        icon: null,
        preview: '',
        order: this.socialLinks.length + 1,
      });
    },

    duplicateSocialLink(index) {
      const original = this.socialLinks[index];
      const uid = Date.now().toString(36) + Math.random().toString(36).substr(2);
      const duplicate = {
        uid,
        title: original.title,
        link: original.link,
        icon: null,
        preview: original.preview || '',
        order: original.order + 1,
      };
      this.socialLinks.splice(index + 1, 0, duplicate);
      this.updateOrders();
    },

    

    moveSocialLinkUp(index) {
      if (index === 0) return;
      const arr = this.socialLinks;
      [arr[index - 1], arr[index]] = [arr[index], arr[index - 1]];
      this.updateOrders();
    },

    moveSocialLinkDown(index) {
      if (index === this.socialLinks.length - 1) return;
      const arr = this.socialLinks;
      [arr[index + 1], arr[index]] = [arr[index], arr[index + 1]];
      this.updateOrders();
    },

    updateOrders() {
      this.socialLinks.forEach((sl, i) => {
        sl.order = i + 1;
      });
    },

    updateSocialLinksOrder() {
      this.updateOrders();
    },

    handleImageChange(event, index) {
      const file = event.target.files[0];
      if (file) {
        this.socialLinks[index].icon = file;

        const reader = new FileReader();
        reader.onload = e => {
          this.socialLinks[index].preview = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },

    async saveSocialLinks() {
      const updates = [];
      const creations = [];
      const deletions = [];
      


      for (const sl of this.socialLinks) {
       
        const formData = new FormData();
        formData.append('title', sl.title);
        formData.append('link', sl.link);
        formData.append('order', sl.order);
        

        if (sl.icon instanceof File) {
          formData.append('icon', sl.icon);
        }
        // Ha nincs új ikon, ne küldjük az icon mezőt, mert null küldése okozza a hibát

        if (sl.id) {
          formData.append('_method', 'PUT');
          updates.push(
            axios.post(`/social-links/${sl.id}`, formData, {
              headers: {
                'Content-Type': 'multipart/form-data',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              }
            })
          );
        } else if (sl.uid) {
          creations.push(
            axios.post(`/social-links`, formData, {
              headers: {
                'Content-Type': 'multipart/form-data',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              }
            })
          );
        }
      }

      for (const id of this.deletedSocialLinks) {
        deletions.push(
          axios.delete(`/social-links/${id}`, {
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
        );

      }


      try {
        await Promise.all([...updates, ...creations, ...deletions]);
        alert('Social links saved successfully!');
        this.deletedSocialLinks = []; 
        await this.fetchSocialLinks(); 

      } catch (error) {
        console.error('Save error:', error);
        alert('Error while saving social links.');
      }
    },

  },
};
</script>
