<template>
    <div class="login-container">
        <div class="login-box">
            <h1>Login Inventaris</h1>

            <p>Silakan masuk untuk melanjutkan.</p>

            <form @submit.prevent="login">
                <div class="form-group">
                    <label>Email</label>
                    <input
                        v-model="email"
                        type="email"
                        placeholder="Masukkan email"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input
                        v-model="password"
                        type="password"
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <div v-if="error" class="error">
                    {{ error }}
                </div>

                <button type="submit" :disabled="loading">
                    {{ loading ? 'Sedang login...' : 'Login' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

const emit = defineEmits(['login-success'])

async function login() {
    error.value = ''
    loading.value = true

    try {
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                email: email.value,
                password: password.value,
            }),
        })

        const result = await response.json()

        if (!response.ok) {
            error.value = result.message || 'Login gagal.'
            return
        }

        localStorage.setItem('token', result.data.token)
        localStorage.setItem(
            'user',
            JSON.stringify(result.data.user)
        )

        emit('login-success', result.data.user)

    } catch (err) {
        error.value = 'Tidak dapat terhubung ke server.'
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.login-container {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #f3f4f6;
}

.login-box {
    width: 350px;
    padding: 30px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

h1 {
    margin-bottom: 10px;
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
    padding: 10px;
    box-sizing: border-box;
}

button {
    width: 100%;
    padding: 10px;
    cursor: pointer;
}

.error {
    margin-bottom: 15px;
    padding: 10px;
    background: #fee2e2;
    color: #b91c1c;
}
</style>