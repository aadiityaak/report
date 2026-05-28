<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-4 sm:p-6 md:p-8 w-full max-w-lg transform transition-all duration-300 max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between mb-4 sm:mb-6">
        <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 dark:text-white">{{ isEdit ? 'Edit Brand' : 'Tambah Brand Baru' }}</h2>
        <button @click="$emit('close')" class="p-1 sm:p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors duration-200 flex-shrink-0">
          <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      
      <form @submit.prevent="handleSubmit" class="space-y-4 sm:space-y-6">
        <!-- Logo Upload -->
        <div class="space-y-2">
          <label for="logo" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Logo Brand</label>
          <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 sm:p-6 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors duration-200">
            <input type="file" id="logo" name="logo" @change="handleFileUpload" accept="image/*" class="hidden" ref="fileInput" />
            <div v-if="!form.logo && !existingLogoUrl" @click="triggerFileInput" class="cursor-pointer">
              <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400 dark:text-gray-500 mb-2 sm:mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
              </svg>
              <p class="text-gray-600 dark:text-gray-300 font-medium text-sm sm:text-base">Klik untuk upload logo</p>
              <p class="text-gray-400 dark:text-gray-500 text-xs sm:text-sm">PNG, JPG, JPEG (Max: 2MB)</p>
            </div>
            <div v-else class="space-y-2 sm:space-y-3">
              <img 
                v-if="previewUrl || existingLogoUrl" 
                :src="previewUrl || `/storage/${existingLogoUrl}`" 
                alt="Preview" 
                class="mx-auto h-16 w-16 sm:h-20 sm:w-20 object-cover rounded-lg" 
              />
              <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ form.logo?.name || 'Logo saat ini' }}</p>
              <div class="flex gap-2 justify-center">
                <button type="button" @click="triggerFileInput" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs sm:text-sm font-medium">
                  {{ form.logo ? 'Ganti file' : 'Ganti logo' }}
                </button>
                <button type="button" @click="removeFile" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs sm:text-sm font-medium">Hapus</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Nama Brand -->
        <div class="space-y-2">
          <label for="nama_brand" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Brand</label>
          <input 
            type="text" 
            id="nama_brand" 
            v-model="form.nama_brand" 
            required 
            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm sm:text-base placeholder-gray-500 dark:placeholder-gray-400"
            placeholder="Masukkan nama brand"
          />
        </div>

        <!-- Nama CV -->
        <div class="space-y-2">
          <label for="pemilik" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama CV</label>
          <input 
            type="text" 
            id="pemilik" 
            v-model="form.pemilik" 
            required 
            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm sm:text-base placeholder-gray-500 dark:placeholder-gray-400"
            placeholder="Masukkan nama CV"
          />
        </div>

        <!-- Deskripsi -->
        <div class="space-y-2">
          <label for="deskripsi" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Deskripsi (Opsional)</label>
          <textarea 
            id="deskripsi" 
            v-model="form.deskripsi" 
            rows="3"
            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm sm:text-base placeholder-gray-500 dark:placeholder-gray-400"
            placeholder="Masukkan deskripsi brand"
          ></textarea>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
          <button 
            type="button" 
            @click="$emit('close')" 
            class="w-full sm:flex-1 px-4 sm:px-6 py-2 sm:py-3 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 text-sm sm:text-base"
          >
            Batal
          </button>
          <button 
            type="submit" 
            class="w-full sm:flex-1 px-4 sm:px-6 py-2 sm:py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium hover:from-blue-700 hover:to-indigo-700 shadow-lg transform hover:scale-105 transition-all duration-200 text-sm sm:text-base"
          >
            {{ isEdit ? 'Update' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, watch, defineProps, defineEmits, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface Brand {
  id?: number;
  nama_brand: string;
  pemilik: string;
  deskripsi?: string;
  logo_path?: string;
}

const props = defineProps<{ 
  open: boolean; 
  isEdit?: boolean; 
  brand?: Brand | null;
}>();

const emit = defineEmits(['submit', 'close']);

const page = usePage();
const currentUser = page.props.auth.user as { name: string; role: string } | undefined;

const form = ref({ 
  nama_brand: '', 
  pemilik: currentUser?.name || '', 
  deskripsi: '',
  logo: null as File | null
});

const fileInput = ref<HTMLInputElement>();
const existingLogoUrl = ref<string>('');

const previewUrl = computed(() => {
  if (form.value.logo) {
    return URL.createObjectURL(form.value.logo);
  }
  return null;
});

// Watch for changes in brand prop to populate form when editing
watch(() => props.brand, (val) => {
  if (val && props.isEdit) {
    form.value = { 
      nama_brand: val.nama_brand || '', 
      pemilik: val.pemilik || '', 
      deskripsi: val.deskripsi || '',
      logo: null
    };
    existingLogoUrl.value = val.logo_path || '';
  } else {
    form.value = { 
      nama_brand: '', 
      pemilik: currentUser?.name || '', 
      deskripsi: '',
      logo: null
    };
    existingLogoUrl.value = '';
  }
}, { immediate: true });

// Reset form when dialog opens/closes
watch(() => props.open, (isOpen) => {
  if (!isOpen) {
    form.value = { 
      nama_brand: '', 
      pemilik: currentUser?.name || '', 
      deskripsi: '',
      logo: null
    };
    existingLogoUrl.value = '';
  }
});

function handleFileUpload(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    form.value.logo = target.files[0];
  }
}

function triggerFileInput() {
  fileInput.value?.click();
}

function removeFile() {
  form.value.logo = null;
  existingLogoUrl.value = '';
  if (fileInput.value) {
    fileInput.value.value = '';
  }
}

function handleSubmit() {
  emit('submit', { ...form.value });
}
</script>
