<template>
    <div>
        <!-- LOGIN -->
        <LoginView
            v-if="!user"
            @login-success="handleLoginSuccess"
        />

        <!-- DASHBOARD -->
        <div v-else>
            <div class="navbar">
                <div>
                    <strong>
                        Sistem Informasi Inventaris
                    </strong>
                </div>

                <div>
                    {{ user.name }}
                    ({{ user.role }})

                    <button @click="logout">
                        Logout
                    </button>
                </div>
            </div>

            <BarangView :user="user" />
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

import LoginView from './components/LoginView.vue'
import BarangView from './components/BarangView.vue'

const user = ref(
    JSON.parse(localStorage.getItem('user') || 'null')
)

function handleLoginSuccess(userData) {
    user.value = userData
}

async function logout() {
    const token = localStorage.getItem('token')

    try {
        await fetch('/api/logout', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
        })
    } catch (error) {
        console.error(error)
    }

    localStorage.removeItem('token')
    localStorage.removeItem('user')

    user.value = null
}
</script>

<style scoped>
.navbar {
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ddd;
}

.navbar button {
    margin-left: 15px;
    padding: 7px 12px;
    cursor: pointer;
}
</style>