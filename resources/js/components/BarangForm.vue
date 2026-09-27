<template>
    <div class="form-container">
        <div class="form-box">
            <h2>
                {{ barang ? 'Edit Barang' : 'Tambah Barang' }}
            </h2>

            <form @submit.prevent="saveBarang">

                <div class="form-group">
                    <label>Kategori ID</label>

                    <input
                        v-model.number="form.kategori_id"
                        type="number"
                        min="1"
                        required
                    />
                </div>

                <div class="form-group">
                    <label>Kode Barang</label>

                    <input
                        v-model="form.kode_barang"
                        type="text"
                        required
                    />
                </div>

                <div class="form-group">
                    <label>Nama Barang</label>

                    <input
                        v-model="form.nama_barang"
                        type="text"
                        required
                    />
                </div>

                <div class="form-group">
                    <label>Stok</label>

                    <input
                        v-model.number="form.stok"
                        type="number"
                        min="0"
                        required
                    />
                </div>

                <!-- Error validasi -->
                <div v-if="error" class="error">
                    {{ error }}

                    <ul v-if="validationErrors">
                        <li
                            v-for="(messages, field) in validationErrors"
                            :key="field"
                        >
                            {{ messages[0] }}
                        </li>
                    </ul>
                </div>

                <div class="actions">
                    <button
                        type="submit"
                        :disabled="loading"
                    >
                        {{ loading ? 'Menyimpan...' : 'Simpan' }}
                    </button>

                    <button
                        type="button"
                        @click="$emit('cancel')"
                    >
                        Batal
                    </button>
                </div>

                <div v-if="success" class="success">
                    {{ success }}
                </div>

            </form>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, watch } from 'vue'

const props = defineProps({
    barang: {
        type: Object,
        default: null,
    },
})

const emit = defineEmits(['saved', 'cancel'])

const form = reactive({
    kategori_id: '',
    kode_barang: '',
    nama_barang: '',
    stok: 0,
})

const loading = ref(false)
const error = ref('')
const success = ref('')
const validationErrors = ref(null)

function fillForm() {
    if (props.barang) {
        form.kategori_id = props.barang.kategori_id
        form.kode_barang = props.barang.kode_barang
        form.nama_barang = props.barang.nama_barang
        form.stok = props.barang.stok
    } else {
        form.kategori_id = ''
        form.kode_barang = ''
        form.nama_barang = ''
        form.stok = 0
    }
}

watch(
    () => props.barang,
    () => {
        fillForm()
        error.value = ''
        validationErrors.value = null
        success.value = ''
    },
    { immediate: true }
)

async function saveBarang() {
    loading.value = true
    error.value = ''
    validationErrors.value = null
    success.value = ''

    const token = localStorage.getItem('token')

    const isEdit = !!props.barang

    const url = isEdit
        ? `/api/barang/${props.barang.id}`
        : '/api/barang'

    const method = isEdit ? 'PUT' : 'POST'

    try {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
            body: JSON.stringify({
                kategori_id: form.kategori_id,
                kode_barang: form.kode_barang,
                nama_barang: form.nama_barang,
                stok: form.stok,
            }),
        })

        const result = await response.json()

        if (!response.ok) {
            error.value = result.message || 'Gagal menyimpan barang.'
            validationErrors.value = result.errors || null
            return
        }

        success.value = result.message

        // Data hasil server berhasil diterima.
        emit('saved', result.data)

    } catch (err) {
        error.value = 'Tidak dapat terhubung ke server.'
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.form-container {
    margin-top: 30px;
}

.form-box {
    max-width: 500px;
    padding: 25px;
    border: 1px solid #ddd;
    background: white;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

input {
    width: 100%;
    padding: 9px;
    box-sizing: border-box;
}

.actions {
    display: flex;
    gap: 10px;
}

button {
    padding: 9px 15px;
    cursor: pointer;
}

.error {
    margin-bottom: 15px;
    padding: 10px;
    background: #fee2e2;
    color: #b91c1c;
}

.success {
    margin-top: 15px;
    padding: 10px;
    background: #dcfce7;
    color: #166534;
}
</style>