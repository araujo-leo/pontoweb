<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';

// --- Imports do Chart.js ---
import { Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from 'chart.js';

// Registrar componentes do gráfico
ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

// --- Props vindas do Backend ---
const props = defineProps({
    stats: Object,          // Totais (earnings, time, counts)
    recentActivity: Array,  // Lista dos últimos 5
    filters: Object,        // Filtros atuais (period, project_id)
    projects: Array,        // Lista para o dropdown
    chartData: Object,      // Dados do gráfico semanal
});

// --- Estado dos Filtros ---
const filterForm = ref({
    period: props.filters.period || 'this_month',
    project_id: props.filters.project_id || '',
});

// Observar mudanças nos filtros para recarregar a página
watch(filterForm, (newVal) => {
    router.get(route('dashboard'), newVal, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, { deep: true });

// --- Formatador de Moeda ---
const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

// --- Configuração do Gráfico (Dados dinâmicos do backend) ---
const chartDataConfig = {
    labels: props.chartData.labels,
    datasets: [
        {
            label: 'Faturamento Semanal',
            backgroundColor: '#10b981', // Verde Emerald
            data: props.chartData.values,
            borderRadius: 4,
        }
    ]
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
    },
    scales: {
        y: { beginAtZero: true, grid: { color: '#374151' } },
        x: { grid: { display: false } }
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Dashboard
                </h2>

                <div class="flex gap-2 w-full md:w-auto">
                    <select
                        v-model="filterForm.project_id"
                        class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Todos os Projetos</option>
                        <option v-for="proj in projects" :key="proj.id" :value="proj.id">
                            {{ proj.name }}
                        </option>
                    </select>

                    <select
                        v-model="filterForm.period"
                        class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="today">Hoje</option>
                        <option value="this_week">Esta Semana</option>
                        <option value="this_month">Este Mês</option>
                        <option value="last_month">Mês Passado</option>
                        <option value="all">Todo o Período</option>
                    </select>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">

                    <Card class="dark:bg-gray-800 dark:border-gray-700">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Faturamento Total
                            </CardTitle>
                            <span class="text-green-500">💰</span>
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ formatCurrency(stats.totalEarnings) }}
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                No período selecionado
                            </p>
                        </CardContent>
                    </Card>

                    <Card class="dark:bg-gray-800 dark:border-gray-700">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Tempo Trabalhado
                            </CardTitle>
                            <span class="text-indigo-500">⏱️</span>
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ stats.totalTime }}
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="dark:bg-gray-800 dark:border-gray-700">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Projetos Ativos
                            </CardTitle>
                            <span class="text-blue-500">📂</span>
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ stats.activeProjects }}
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="dark:bg-gray-800 dark:border-gray-700">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Registros
                            </CardTitle>
                            <span class="text-orange-500">📝</span>
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ stats.entriesCount }}
                            </div>
                        </CardContent>
                    </Card>

                </div>

                <div class="grid gap-4 md:grid-cols-1 lg:grid-cols-3">

                    <Card class="lg:col-span-2 dark:bg-gray-800 dark:border-gray-700">
                        <CardHeader>
                            <CardTitle class="dark:text-white">Desempenho Financeiro</CardTitle>
                            <CardDescription>Visão geral dos ganhos estimados.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="h-[300px]">
                                <Bar :data="chartDataConfig" :options="chartOptions" />
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="dark:bg-gray-800 dark:border-gray-700">
                        <CardHeader>
                            <CardTitle class="dark:text-white">Atividade Recente</CardTitle>
                            <CardDescription>Últimos 5 registros.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-6">
                                <div v-for="activity in recentActivity" :key="activity.id" class="flex items-center justify-between">
                                    <div class="space-y-1">
                                        <p class="text-sm font-medium leading-none dark:text-white">
                                            {{ activity.project_name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ activity.date }} • {{ activity.duration }}
                                        </p>
                                    </div>
                                    <div class="font-medium text-sm" :class="activity.earnings > 0 ? 'text-green-600' : 'text-gray-500'">
                                        {{ activity.earnings > 0 ? formatCurrency(activity.earnings) : '...' }}
                                    </div>
                                </div>

                                <div v-if="recentActivity.length === 0" class="text-center text-gray-500 text-sm py-4">
                                    Nenhuma atividade encontrada neste período.
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
