<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Variável reativa para guardar o ranking
const ranking = ref([]);
const loading = ref(true);

// Função para formatar o tempo (ex: 90s -> 1m 30s)
const formatTime = (seconds) => {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return m > 0 ? `${m}m ${s}s` : `${s}s`;
};

// Busca os dados assim que a tela carrega
onMounted(async () => {
    try {
        const response = await axios.get('/api/ranking');
        ranking.value = response.data;
    } catch (error) {
        console.error("Erro ao carregar ranking", error);
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Está pronto para testar seus conhecimentos?</h3>
                        <p class="text-gray-600">Inicie um novo quiz agora e suba no ranking!</p>
                    </div>
                    
                    <Link 
                        :href="route('quiz')" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition"
                    >
                        Iniciar Novo Quiz
                    </Link>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                            🏆 Top 10 Melhores Jogadores
                        </h3>

                        <div v-if="loading" class="text-center py-4 text-gray-500">
                            Carregando ranking...
                        </div>

                        <table v-else class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="p-3 text-sm font-semibold text-gray-600">Posição</th>
                                    <th class="p-3 text-sm font-semibold text-gray-600">Jogador</th>
                                    <th class="p-3 text-sm font-semibold text-gray-600">Pontuação</th>
                                    <th class="p-3 text-sm font-semibold text-gray-600">Tempo</th>
                                    <th class="p-3 text-sm font-semibold text-gray-600">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(attempt, index) in ranking" :key="attempt.id" class="border-b hover:bg-gray-50">
                                    <td class="p-3 font-bold text-gray-700">#{{ index + 1 }}</td>
                                    <td class="p-3">
                                        <div class="font-medium text-gray-900">{{ attempt.user.name }}</div>
                                    </td>
                                    <td class="p-3 text-green-600 font-bold">{{ attempt.score }} pts</td>
                                    <td class="p-3 text-gray-500">{{ formatTime(attempt.total_time) }}</td>
                                    <td class="p-3 text-sm text-gray-400">
                                        {{ new Date(attempt.created_at).toLocaleDateString() }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-if="!loading && ranking.length === 0" class="text-center py-6 text-gray-500">
                            Ninguém jogou ainda. Seja o primeiro!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>