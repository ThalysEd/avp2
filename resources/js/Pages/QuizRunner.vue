<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import axios from 'axios';

// --- ESTADOS ---
const loading = ref(true);
const quizFinished = ref(false);
const questions = ref([]);
const currentQuestionIndex = ref(0);
const selectedOptionId = ref(null);
const userAnswers = ref([]); // Guarda as respostas para enviar no final
const resultData = ref(null); // Guarda o retorno do backend (nota)

// --- TIMER ---
const totalTime = ref(0);
let timerInterval = null;

const startTimer = () => {
    timerInterval = setInterval(() => {
        totalTime.value++;
    }, 1000);
};

const stopTimer = () => {
    clearInterval(timerInterval);
};

// Formata segundos em 00:00
const formattedTime = computed(() => {
    const m = Math.floor(totalTime.value / 60).toString().padStart(2, '0');
    const s = (totalTime.value % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
});

// --- LÓGICA DO JOGO ---

// 1. Carrega o Quiz
onMounted(async () => {
    try {
        const response = await axios.get('/api/quiz/start');
        questions.value = response.data;
        loading.value = false;
        startTimer();
    } catch (error) {
        alert('Erro ao carregar perguntas. Tente recarregar a página.');
    }
});

onUnmounted(() => stopTimer());

// 2. Seleciona Opção
const selectOption = (id) => {
    selectedOptionId.value = id;
};

// 3. Avança ou Finaliza
const nextQuestion = async () => {
    // Salva a resposta atual
    userAnswers.value.push({
        question_id: questions.value[currentQuestionIndex.value].id,
        option_id: selectedOptionId.value
    });

    selectedOptionId.value = null; // Limpa seleção

    // Se ainda tem perguntas, avança
    if (currentQuestionIndex.value < questions.value.length - 1) {
        currentQuestionIndex.value++;
    } else {
        // Se acabou, envia tudo
        await submitQuiz();
    }
};

// 4. Envia para o Backend
const submitQuiz = async () => {
    stopTimer();
    loading.value = true;
    try {
        const response = await axios.post('/api/quiz/submit', {
            answers: userAnswers.value,
            time: totalTime.value
        });
        
        resultData.value = response.data;
        quizFinished.value = true;
    } catch (error) {
        console.error(error);
        alert('Erro ao enviar respostas.');
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Head title="Quiz em Andamento" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                
                <div v-if="loading" class="text-center p-10 bg-white rounded shadow">
                    <p class="text-xl text-gray-500 animate-pulse">
                        {{ quizFinished ? 'Calculando resultados...' : 'Carregando perguntas...' }}
                    </p>
                </div>

                <div v-else-if="!quizFinished" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    
                    <div class="flex justify-between items-center mb-6 border-b pb-4">
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-wide">
                            Questão {{ currentQuestionIndex + 1 }} de {{ questions.length }}
                        </span>
                        <div class="flex items-center gap-2 text-blue-600 font-mono text-lg font-bold bg-blue-50 px-3 py-1 rounded">
                            🕒 {{ formattedTime }}
                        </div>
                    </div>

                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-8 leading-snug">
                        {{ questions[currentQuestionIndex].statement }}
                    </h2>

                    <div class="flex flex-col gap-4">
                        <button 
                            v-for="option in questions[currentQuestionIndex].options" 
                            :key="option.id"
                            @click="selectOption(option.id)"
                            :class="[
                                'w-full text-left p-4 rounded-lg border-2 transition-all duration-200 text-lg',
                                selectedOptionId === option.id 
                                    ? 'border-blue-500 bg-blue-50 text-blue-800 shadow-md transform scale-[1.01]' 
                                    : 'border-gray-200 hover:border-blue-300 hover:bg-gray-50 text-gray-700'
                            ]"
                        >
                            {{ option.text }}
                        </button>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button 
                            @click="nextQuestion" 
                            :disabled="!selectedOptionId"
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3 px-8 rounded-lg shadow transition"
                        >
                            {{ currentQuestionIndex === questions.length - 1 ? 'Finalizar Quiz' : 'Próxima' }}
                        </button>
                    </div>
                </div>

                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center">
                    <div class="mb-6">
                        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto text-4xl mb-4">
                            🏆
                        </div>
                        <h2 class="text-3xl font-bold text-gray-800">Quiz Finalizado!</h2>
                        <p class="text-gray-600 mt-2">Veja como foi seu desempenho:</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto mb-8">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Pontuação</p>
                            <p class="text-3xl font-bold text-blue-600">{{ resultData.score }} pts</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Tempo Total</p>
                            <p class="text-3xl font-bold text-blue-600">{{ formattedTime }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Acertos</p>
                            <p class="text-xl font-bold text-green-600">{{ resultData.stats.correct_answers }}</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Erros</p>
                            <p class="text-xl font-bold text-red-600">{{ resultData.stats.wrong_answers }}</p>
                        </div>
                    </div>

                    <Link 
                        :href="route('dashboard')" 
                        class="inline-block bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-6 rounded-lg transition"
                    >
                        Voltar ao Ranking
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>