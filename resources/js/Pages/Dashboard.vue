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

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

const props = defineProps({
    stats: Object,
    recentActivity: Array,
    filters: Object,
    projects: Array,
    chartData: Object,
});

const filterForm = ref({
    period: props.filters.period || 'this_month',
    project_id: props.filters.project_id || '',
});

watch(filterForm, (newVal) => {
    router.get(route('dashboard'), newVal, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, { deep: true });

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

const chartDataConfig = {
    labels: props.chartData.labels,
    datasets: [
        {
            label: 'Faturamento',
            backgroundColor: '#10b981', // Emerald 500
            data: props.chartData.values,
            borderRadius: 8,
            barThickness: 12,
        }
    ]
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#18181b', // Zinc 900
            padding: 12,
            titleFont: { size: 14 },
            bodyFont: { size: 13 },
            displayColors: false
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: '#f4f4f5' },
            ticks: { color: '#71717a' }
        },
        x: {
            grid: { display: false },
            ticks: { color: '#71717a' }
        }
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-bold text-2xl text-zinc-900 leading-tight">
                        Dashboard
                    </h2>
                    <p class="text-sm text-zinc-500 mt-1">Bem-vindo de volta! Aqui está o resumo dos seus projetos.</p>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <select
                        v-model="filterForm.project_id"
                        class="h-10 rounded-full border-zinc-200 bg-white px-4 py-1 text-sm font-medium text-zinc-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 transition-all"
                    >
                        <option value="">Todos os Projetos</option>
                        <option v-for="proj in projects" :key="proj.id" :value="proj.id">
                            {{ proj.name }}
                        </option>
                    </select>

                    <select
                        v-model="filterForm.period"
                        class="h-10 rounded-full border-zinc-200 bg-white px-4 py-1 text-sm font-medium text-zinc-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 transition-all"
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

        <div class="py-10 bg-zinc-50/50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <Card class="border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-200/60 rounded-2xl overflow-hidden">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-semibold text-zinc-500 uppercase tracking-wider">Faturamento</CardTitle>
                            <div class="h-8 w-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="text-3xl font-bold text-zinc-900 tracking-tight">{{ formatCurrency(stats.totalEarnings) }}</div>
                            <p class="text-xs text-emerald-600 font-medium mt-1">Ganhos no período</p>
                        </CardContent>
                    </Card>

                    <Card class="border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-200/60 rounded-2xl overflow-hidden">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-semibold text-zinc-500 uppercase tracking-wider">Tempo</CardTitle>
                            <div class="h-8 w-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="text-3xl font-bold text-zinc-900 tracking-tight">{{ stats.totalTime }}</div>
                            <p class="text-xs text-zinc-500 mt-1">Horas trabalhadas</p>
                        </CardContent>
                    </Card>

                    <Card class="border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-200/60 rounded-2xl overflow-hidden">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-semibold text-zinc-500 uppercase tracking-wider">Projetos</CardTitle>
                            <div class="h-8 w-8 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="text-3xl font-bold text-zinc-900 tracking-tight">{{ stats.activeProjects }}</div>
                            <p class="text-xs text-zinc-500 mt-1">Contratos ativos</p>
                        </CardContent>
                    </Card>

                    <Card class="border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-200/60 rounded-2xl overflow-hidden">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-semibold text-zinc-500 uppercase tracking-wider">Registros</CardTitle>
                            <div class="h-8 w-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="text-3xl font-bold text-zinc-900 tracking-tight">{{ stats.entriesCount }}</div>
                            <p class="text-xs text-zinc-500 mt-1">Total de apontamentos</p>
                        </CardContent>
                    </Card>
                </div>

                <div class="grid gap-8 lg:grid-cols-3">
                    <Card class="lg:col-span-2 border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-200/60 rounded-2xl bg-white">
                        <CardHeader>
                            <CardTitle class="text-lg font-bold text-zinc-900">Desempenho Financeiro</CardTitle>
                            <CardDescription>Visualização detalhada dos ganhos estimados.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="h-[350px] w-full pt-4">
                                <Bar :data="chartDataConfig" :options="chartOptions" />
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-200/60 rounded-2xl bg-white">
                        <CardHeader>
                            <CardTitle class="text-lg font-bold text-zinc-900">Atividade Recente</CardTitle>
                            <CardDescription>Seus últimos 5 logs de tempo.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-6">
                                <div v-for="activity in recentActivity" :key="activity.id"
                                     class="flex items-center justify-between group cursor-pointer"
                                >
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-full bg-zinc-100 flex items-center justify-center text-zinc-500 font-bold text-xs group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-colors">
                                            {{ activity.project_name.charAt(0) }}
                                        </div>
                                        <div class="space-y-0.5">
                                            <p class="text-sm font-semibold text-zinc-900 leading-none">
                                                {{ activity.project_name }}
                                            </p>
                                            <p class="text-xs text-zinc-500">
                                                {{ activity.date }} • {{ activity.duration }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold" :class="activity.earnings > 0 ? 'text-emerald-600' : 'text-zinc-400'">
                                        {{ activity.earnings > 0 ? formatCurrency(activity.earnings) : '--' }}
                                    </div>
                                </div>

                                <div v-if="recentActivity.length === 0" class="text-center py-10">
                                    <p class="text-sm text-zinc-400">Nenhuma atividade registrada.</p>
                                </div>
                            </div>

                            <button class="w-full mt-8 py-3 rounded-xl border border-zinc-100 bg-zinc-50 text-sm font-semibold text-zinc-600 hover:bg-zinc-100 transition-all">
                                Ver todos os registros
                            </button>
                        </CardContent>
                    </Card>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
