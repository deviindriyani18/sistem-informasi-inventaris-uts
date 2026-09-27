<template>
    <div class="barang-page">
        <div class="header">
            <div>
                <h1>Data Barang</h1>
                <p>
                    Kelola data barang inventaris.
                </p>
            </div>

            <button
                v-if="user.role === 'admin'"
                @click="showForm = true"
            >
                + Tambah Barang
            </button>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="info">
            Memuat data barang...
        </div>

        <!-- Error -->
        <div v-else-if="error" class="error">
            {{ error }}
        </div>

        <!-- Empty -->
        <div v-else-if="barangs.length === 0" class="info">
            Belum ada data barang.
        </div>

        <!-- Table -->
        <table v-else>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th v-if="user.role === 'admin'">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr
                    v-for="(barang, index) in barangs"
                    :key="barang.id"
                >
                    <td>{{ index + 1 }}</td>
                    <td>{{ barang.kode_barang }}</td>
                    <td>{{ barang.nama_barang }}</td>
                    <td>
                        {{ barang.kategori?.nama_kategori ?? '-' }}
                    </td>
                    <td>{{ barang.stok }}</td>

                    <td v-if="user.role === 'admin'">
                        <button @click="editBarang(barang)">
                            Edit
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Form -->
        <BarangForm
            v-if="showForm"
            :barang="selectedBarang"
            @saved="handleSaved"
            @cancel="closeForm"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import BarangForm from './BarangForm.vue'

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const barangs = ref([])
const loading = ref(false)
const error = ref('')
const showForm = ref(false)
const selectedBarang = ref(null)

const token = localStorage.getItem('token')

async function loadBarang() {
    loading.value = true
    error.value = ''

    try {
        const response = await fetch('/api/barang', {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
        })

        const result = await response.json()

        if (!response.ok) {
            error.value = result.message || 'Gagal mengambil data barang.'
            return
        }

        barangs.value = result.data

    } catch (err) {
        error.value = 'Tidak dapat terhubung ke server.'
    } finally {
        loading.value = false
    }
}

function editBarang(barang) {
    selectedBarang.value = barang
    showForm.value = true
}

function closeForm() {
    showForm.value = false
    selectedBarang.value = null
}

async function handleSaved() {
    closeForm()
    await loadBarang()
}

onMounted(() => {
    loadBarang()
})
</script>

<style scoped>
.barang-page {
    padding: 30px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

button {
    padding: 8px 14px;
    cursor: pointer;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}

th {
    background: #f3f4f6;
}

.info {
    padding: 15px;
    background: #f3f4f6;
}

.error {
    padding: 15px;
    background: #fee2e2;
    color: #b91c1c;
}
</style>