<template>
  <AppLayout :breadcrumbs="[{ title: 'Brand Ownership', href: '/admin/brand-ownership' }]">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="grid auto-rows-min gap-4 md:grid-cols-1">
        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <div class="p-4 md:p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
              <div>
                <h1 class="text-xl md:text-2xl font-bold mb-2 text-gray-900 dark:text-white">Atur Brand Owner</h1>
                <p class="text-gray-600 dark:text-gray-300">Kelola relasi antara brand dengan brand owner</p>
              </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4 mb-6">
              <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-300">Total Brand</p>
                    <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">{{ brands.length }}</p>
                  </div>
                  <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-full">
                    <svg class="w-4 h-4 md:w-5 md:h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-300">Sudah Ada Owner</p>
                    <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">{{ assignedCount }}</p>
                  </div>
                  <div class="p-2 bg-green-100 dark:bg-green-800 rounded-full">
                    <svg class="w-4 h-4 md:w-5 md:h-5 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-3 md:p-4 border-l-4 border-yellow-500">
                <div class="flex items-center">
                  <div class="flex-1">
                    <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-300">Brand Owner</p>
                    <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">{{ branOwners.length }}</p>
                  </div>
                  <div class="p-2 bg-yellow-100 dark:bg-yellow-800 rounded-full">
                    <svg class="w-4 h-4 md:w-5 md:h-5 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 616 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Brand Owner List -->
            <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
              <div class="p-4 md:p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daftar Brand Owner</h2>

                <div v-if="branOwners.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                  <p>Belum ada user dengan role brand owner.</p>
                </div>

                <div v-else class="flex flex-wrap gap-2 mb-6">
                  <span
                    v-for="owner in branOwners"
                    :key="owner.id"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ owner.name }}
                    <span class="text-xs opacity-75">({{ owner.email }})</span>
                  </span>
                </div>
              </div>
            </div>

            <!-- Brands Table -->
            <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border mt-4">
              <div class="p-4 md:p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Atur Kepemilikan Brand</h2>

                <div class="overflow-x-auto">
                  <table class="w-full text-left border-collapse">
                    <thead>
                      <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="pb-3 text-sm font-medium text-gray-600 dark:text-gray-300">Nama Brand</th>
                        <th class="pb-3 text-sm font-medium text-gray-600 dark:text-gray-300">Pemilik (CV)</th>
                        <th class="pb-3 text-sm font-medium text-gray-600 dark:text-gray-300">Brand Owner Saat Ini</th>
                        <th class="pb-3 text-sm font-medium text-gray-600 dark:text-gray-300">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="brand in brands" :key="brand.id" class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="py-3 text-sm text-gray-900 dark:text-white font-medium">{{ brand.nama_brand }}</td>
                        <td class="py-3 text-sm text-gray-600 dark:text-gray-300">{{ brand.pemilik }}</td>
                        <td class="py-3 text-sm">
                          <span v-if="editingBrandId === brand.id">
                            <select
                              v-model="editForm.user_id"
                              class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                            >
                              <option :value="null">Tidak Ada</option>
                              <option v-for="owner in branOwners" :key="owner.id" :value="owner.id">
                                {{ owner.name }} ({{ owner.email }})
                              </option>
                            </select>
                          </span>
                          <span v-else class="text-gray-900 dark:text-white">
                            <template v-if="brand.owner_name">
                              {{ brand.owner_name }}
                              <span class="text-gray-500 dark:text-gray-400">({{ brand.owner_email }})</span>
                            </template>
                            <span v-else class="text-gray-400 dark:text-gray-500 italic">Belum ada owner</span>
                          </span>
                        </td>
                        <td class="py-3">
                          <div v-if="editingBrandId === brand.id" class="flex gap-2">
                            <button
                              @click="saveAssignment(brand.id)"
                              :disabled="saving"
                              class="px-3 py-1.5 rounded text-xs bg-blue-600 text-white hover:bg-blue-700 transition-colors disabled:opacity-50"
                            >
                              {{ saving ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                            <button
                              @click="cancelEdit"
                              class="px-3 py-1.5 rounded text-xs bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
                            >
                              Batal
                            </button>
                          </div>
                          <button
                            v-else
                            @click="startEdit(brand)"
                            class="px-3 py-1.5 rounded text-xs bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-800 dark:text-yellow-200 dark:hover:bg-yellow-700 transition-colors"
                          >
                            Atur Owner
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div v-if="brands.length === 0" class="text-center py-12 text-gray-500 dark:text-gray-400">
                  <p class="text-sm">Belum ada data brand.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Success/Error Notification -->
      <div v-if="notification.show" class="fixed top-4 left-4 right-4 sm:left-auto sm:right-4 sm:w-auto z-50 transform transition-all duration-300">
        <div
          :class="[
            'px-4 sm:px-6 py-3 sm:py-4 rounded-lg shadow-lg flex items-center gap-3 border',
            notification.type === 'success'
              ? 'bg-green-100 border-green-400 text-green-700'
              : 'bg-red-100 border-red-400 text-red-700'
          ]"
        >
          <svg v-if="notification.type === 'success'" class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <svg v-else class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span class="font-medium text-sm sm:text-base flex-1">{{ notification.message }}</span>
          <button @click="notification.show = false" class="flex-shrink-0 opacity-70 hover:opacity-100">
            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

interface BrandOwner {
  id: number;
  name: string;
  email: string;
}

interface BrandItem {
  id: number;
  nama_brand: string;
  pemilik: string;
  deskripsi?: string;
  user_id: number | null;
  owner_name: string | null;
  owner_email: string | null;
}

const props = defineProps<{
  brands: BrandItem[];
  branOwners: BrandOwner[];
}>();

const page = usePage();

const assignedCount = computed(() => props.brands.filter(b => b.user_id !== null).length);

const editingBrandId = ref<number | null>(null);
const editForm = ref<{ user_id: number | null }>({ user_id: null });
const saving = ref(false);
const notification = ref<{ show: boolean; type: 'success' | 'error'; message: string }>({
  show: false,
  type: 'success',
  message: '',
});

onMounted(() => {
  const flash = (page.props as any).flash;
  if (flash?.success) {
    showNotification('success', flash.success);
  } else if (flash?.error) {
    showNotification('error', flash.error);
  }
});

function startEdit(brand: BrandItem) {
  editingBrandId.value = brand.id;
  editForm.value = { user_id: brand.user_id };
}

function cancelEdit() {
  editingBrandId.value = null;
  editForm.value = { user_id: null };
}

function saveAssignment(brandId: number) {
  saving.value = true;
  router.put(`/admin/brand-ownership/${brandId}`, editForm.value, {
    onSuccess: () => {
      editingBrandId.value = null;
      saving.value = false;
    },
    onError: (errors) => {
      saving.value = false;
      const errorMsg = Object.values(errors).flat().join(', ');
      showNotification('error', errorMsg || 'Terjadi kesalahan saat menyimpan.');
    },
    onFinish: () => {
      saving.value = false;
    },
  });
}

function showNotification(type: 'success' | 'error', message: string) {
  notification.value = { show: true, type, message };
  setTimeout(() => {
    notification.value.show = false;
  }, 3000);
}
</script>
