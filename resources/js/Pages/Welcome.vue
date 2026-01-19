<script setup>
import { Head } from '@inertiajs/vue3';
import {
    Play,
    Pause,
    TrendingUp,
    DollarSign,
    Clock,
    Briefcase,
    AlertCircle
} from 'lucide-vue-next';

// Componentes "Shadcn-like" (assumindo que você tem eles importados)
import Card from '@/Components/Ui/Card.vue';
import Button from '@/Components/Ui/Button.vue';
import ProgressBar from '@/Components/Ui/ProgressBar.vue';

defineProps({
    user: Object,
    financialSummary: {
        type: Object,
        default: () => ({
            current_revenue: 4520.00,
            projected_revenue: 8200.00,
            monthly_goal: 10000.00,
            unpaid_invoices: 1250.00,
            hours_tracked: 84.5
        })
    },
    activeTimer: {
        type: Object,
        default: null // ou { project: 'Landing Page', client: 'Acme Corp', start_time: '...' }
    },
    projects: Array
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};
</script>

<template>
    <Head title="Dashboard" />

    <div class="p-6 space-y-8 bg-gray-50/50 dark:bg-zinc-900 min-h-screen font-sans">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Olá, {{ user.name }} 👋
                </h1>
                <p class="text-gray-500 dark:text-gray-400">
                    Vamos bater a meta de hoje?
                </p>
            </div>

            <div class="w-full md:w-auto">
                <div v-if="activeTimer" class="flex items-center gap-4 bg-emerald-100 border border-emerald-200 p-2 rounded-lg pr-4 animate-pulse-slow">
                    <div class="bg-emerald-500 text-white p-2 rounded-md">
                        <Clock class="w-5 h-5 animate-spin-slow" />
                    </div>
                    <div>
                        <p class="text-xs text-emerald-800 font-bold uppercase tracking-wider">Gravando agora</p>
                        <p class="text-sm font-semibold text-emerald-900">{{ activeTimer.client }} - {{ activeTimer.project }}</p>
                    </div>
                    <Button variant="destructive" size="sm" class="ml-auto">
                        <Pause class="w-4 h-4 mr-2" /> Parar
                    </Button>
                </div>

                <Button v-else size="lg" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/20 text-white">
                    <Play class="w-5 h-5 mr-2" /> Iniciar Trabalho
                </Button>
            </div>
        </div>

        <Card class="p-6 border-indigo-100 dark:border-indigo-900 bg-gradient-to-r from-white to-indigo-50/30 dark:from-zinc-900 dark:to-zinc-800/50">
            <div class="flex justify-between items-end mb-2">
                <div>
                    <span class="text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-900/30 px-2 py-1 rounded-full">
                        Meta Mensal
                    </span>
                    <div class="mt-2 text-4xl font-extrabold text-gray-900 dark:text-white">
                        {{ formatCurrency(financialSummary.current_revenue) }}
                        <span class="text-lg text-gray-400 font-normal">/ {{ formatCurrency(financialSummary.monthly_goal) }}</span>
                    </div>
                </div>
                <div class="text-right hidden sm:block">
                    <p class="text-sm text-gray-500">Projeção estimada</p>
                    <p class="text-xl font-bold text-gray-700 dark:text-gray-300">{{ formatCurrency(financialSummary.projected_revenue) }}</p>
                </div>
            </div>
            <div class="relative w-full h-4 bg-gray-200 rounded-full overflow-hidden">
                <div
                    class="absolute top-0 left-0 h-full bg-indigo-600 transition-all duration-1000 ease-out"
                    :style="{ width: `${(financialSummary.current_revenue / financialSummary.monthly_goal) * 100}%` }"
                ></div>
                <div
                    class="absolute top-0 left-0 h-full bg-indigo-300 opacity-50 transition-all duration-1000 ease-out"
                    :style="{ width: `${(financialSummary.projected_revenue / financialSummary.monthly_goal) * 100}%` }"
                ></div>
            </div>
            <p class="text-xs text-gray-500 mt-2 text-right">
                Você já atingiu {{ Math.round((financialSummary.current_revenue / financialSummary.monthly_goal) * 100) }}% da sua meta!
            </p>
        </Card>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <Card class="p-6 flex items-center space-x-4 hover:border-gray-300 transition-colors">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-full">
                    <Clock class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Horas este mês</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ financialSummary.hours_tracked }}h</h3>
                </div>
            </Card>

            <Card class="p-6 flex items-center space-x-4 border-l-4 border-l-orange-500">
                <div class="p-3 bg-orange-100 dark:bg-orange-900/30 text-orange-600 rounded-full">
                    <AlertCircle class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pendente de Pagamento</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(financialSummary.unpaid_invoices) }}</h3>
                    <button class="text-xs text-orange-600 font-semibold hover:underline">Cobrar clientes →</button>
                </div>
            </Card>

            <Card class="p-6 flex items-center space-x-4">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 text-purple-600 rounded-full">
                    <TrendingUp class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sua hora média</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ formatCurrency(financialSummary.current_revenue / financialSummary.hours_tracked) }}/h
                    </h3>
                </div>
            </Card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <Card class="col-span-2 p-6 min-h-[300px]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg">Performance dos últimos 7 dias</h3>
                    <select class="text-sm border-gray-200 rounded-md">
                        <option>Receita</option>
                        <option>Horas</option>
                    </select>
                </div>
                <div class="flex items-center justify-center h-48 bg-gray-50 rounded border border-dashed border-gray-200 text-gray-400">
                    [AreaChart Component: Receita x Dia]
                </div>
            </Card>

            <Card class="col-span-1 p-0 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-semibold text-gray-900">Top Clientes (Mês)</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div v-for="client in [1,2,3]" :key="client" class="p-4 flex justify-between items-center hover:bg-gray-50 transition cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-gray-200 flex items-center justify-center font-bold text-xs text-gray-600">
                                CL
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Tech Solutions</p>
                                <p class="text-xs text-gray-500">22h trabalhadas</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">R$ 2.200</p>
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Ativo</span>
                        </div>
                    </div>
                </div>
                <div class="p-3 bg-gray-50 text-center">
                    <Link href="/clients" class="text-sm text-indigo-600 font-medium hover:text-indigo-800">Ver todos os clientes</Link>
                </div>
            </Card>
        </div>
    </div>
</template>
