<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    entries: Array,
    totalEarnings: Number,
    totalTime: String,
    projects: Array,
    activitySummaries: Array,
    filters: Object,
});

const filterForm = ref({
    period: props.filters.period || '',
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || '',
    project_id: props.filters.project_id || '',
});

watch(filterForm, (newVal) => {
    router.get(route('time-entries.index'), newVal, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, { deep: true });

const setPeriod = (period) => {
    const today = new Date();
    let startDate = '';
    let endDate = '';

    switch(period) {
        case 'today':
            startDate = endDate = today.toISOString().split('T')[0];
            break;
        case 'this_week':
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay());
            startDate = startOfWeek.toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
        case 'last_week':
            const lastWeekEnd = new Date(today);
            lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
            const lastWeekStart = new Date(lastWeekEnd);
            lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
            startDate = lastWeekStart.toISOString().split('T')[0];
            endDate = lastWeekEnd.toISOString().split('T')[0];
            break;
        case 'this_month':
            const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            startDate = startOfMonth.toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
        case 'last_month':
            const lastMonthStart = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
            startDate = lastMonthStart.toISOString().split('T')[0];
            endDate = lastMonthEnd.toISOString().split('T')[0];
            break;
    }

    filterForm.value.period = period;
    filterForm.value.start_date = startDate;
    filterForm.value.end_date = endDate;
};

const clearFilters = () => {
    filterForm.value = {
        period: '',
        start_date: '',
        end_date: '',
        project_id: '',
    };
};

const exportCsv = () => {
    const params = new URLSearchParams(filterForm.value);
    window.location.href = route('time-entries.export-csv') + '?' + params.toString();
};

const exportPdf = () => {
    const params = new URLSearchParams(filterForm.value);
    window.location.href = route('time-entries.export-pdf') + '?' + params.toString();
};

const editingDescription = ref(null);
const descriptionForm = useForm({
    description: '',
});

const editingEntry = ref(null);
const manualEditForm = useForm({
    start_time: '',
    end_time: '',
    description: '',
    activity_type: 'development',
});

const attachmentForm = useForm({
    file: null,
});

const startEditDescription = (entryId, currentDescription) => {
    editingDescription.value = entryId;
    descriptionForm.description = currentDescription || '';
};

const saveDescription = (entryId) => {
    descriptionForm.patch(route('time-entries.update-description', entryId), {
        preserveScroll: true,
        onSuccess: () => {
            editingDescription.value = null;
            descriptionForm.reset();
        }
    });
};

const cancelEdit = () => {
    editingDescription.value = null;
    descriptionForm.reset();
};

const startManualEdit = (entry) => {
    editingEntry.value = entry.id;

    // Convert date and time back to datetime-local format
    const dateStr = entry.date.split('/').reverse().join('-'); // Convert dd/mm/yyyy to yyyy-mm-dd
    manualEditForm.start_time = `${dateStr}T${entry.start_time}`;
    manualEditForm.end_time = entry.end_time !== '...' ? `${dateStr}T${entry.end_time}` : '';
    manualEditForm.description = entry.description || '';
    manualEditForm.activity_type = entry.activity_type || 'development';
};

const saveManualEdit = (entryId) => {
    manualEditForm.patch(route('time-entries.manual-edit', entryId), {
        preserveScroll: true,
        onSuccess: () => {
            editingEntry.value = null;
            manualEditForm.reset();
        }
    });
};

const cancelManualEdit = () => {
    editingEntry.value = null;
    manualEditForm.reset();
};

const handleFileUpload = (event, entryId) => {
    const file = event.target.files[0];
    if (file) {
        attachmentForm.file = file;
        attachmentForm.post(route('time-entries.upload-attachment', entryId), {
            preserveScroll: true,
            onSuccess: () => {
                attachmentForm.reset();
            }
        });
    }
};

const deleteAttachment = (entryId, index) => {
    if (confirm('Tem certeza que deseja remover este anexo?')) {
        router.delete(route('time-entries.delete-attachment', entryId), {
            data: { index },
            preserveScroll: true,
        });
    }
};

const getActivityTypeLabel = (type) => {
    const labels = {
        'development': 'Development',
        'maintenance': 'Maintenance',
        'meeting': 'Meeting',
        'research': 'Research',
        'documentation': 'Documentation',
        'review': 'Review',
        'support': 'Support',
        'planning': 'Planning'
    };
    return labels[type] || type;
};

const getActivityTypeColor = (type) => {
    const colors = {
        'development': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'maintenance': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
        'meeting': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
        'research': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'documentation': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'review': 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
        'support': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'planning': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300'
    };
    return colors[type] || colors['development'];
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};
</script>

<template>
    <Head title="Extrato de Horas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Extrato de Horas
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Filters Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <!-- Predefined Periods -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Período Rápido
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <button
                                @click="setPeriod('today')"
                                :class="[
                                    'px-4 py-2 text-sm rounded-md transition',
                                    filterForm.period === 'today'
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                                ]"
                            >
                                Hoje
                            </button>
                            <button
                                @click="setPeriod('this_week')"
                                :class="[
                                    'px-4 py-2 text-sm rounded-md transition',
                                    filterForm.period === 'this_week'
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                                ]"
                            >
                                Esta Semana
                            </button>
                            <button
                                @click="setPeriod('last_week')"
                                :class="[
                                    'px-4 py-2 text-sm rounded-md transition',
                                    filterForm.period === 'last_week'
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                                ]"
                            >
                                Semana Passada
                            </button>
                            <button
                                @click="setPeriod('this_month')"
                                :class="[
                                    'px-4 py-2 text-sm rounded-md transition',
                                    filterForm.period === 'this_month'
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                                ]"
                            >
                                Este Mês
                            </button>
                            <button
                                @click="setPeriod('last_month')"
                                :class="[
                                    'px-4 py-2 text-sm rounded-md transition',
                                    filterForm.period === 'last_month'
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                                ]"
                            >
                                Mês Passado
                            </button>
                        </div>
                    </div>

                    <!-- Custom Date Filters -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Período Personalizado
                        </label>
                        <div class="flex flex-col md:flex-row gap-4 items-end">
                            <div class="flex-1 space-y-4 md:space-y-0 md:flex md:gap-4">
                                <div class="flex-1">
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                        Data Inicial
                                    </label>
                                    <input
                                        v-model="filterForm.start_date"
                                        @input="filterForm.period = ''"
                                        type="date"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>

                                <div class="flex-1">
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                        Data Final
                                    </label>
                                    <input
                                        v-model="filterForm.end_date"
                                        @input="filterForm.period = ''"
                                        type="date"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    />
                                </div>

                                <div class="flex-1">
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                                        Projeto
                                    </label>
                                    <select
                                        v-model="filterForm.project_id"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                        <option value="">Todos os Projetos</option>
                                        <option v-for="project in projects" :key="project.id" :value="project.id">
                                            {{ project.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button
                                    @click="clearFilters"
                                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-md transition"
                                >
                                    Limpar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex gap-2 justify-end">
                            <button
                                @click="exportCsv"
                                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-md transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Exportar CSV
                            </button>
                            <button
                                @click="exportPdf"
                                class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Exportar PDF
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500 dark:text-gray-400 text-sm uppercase font-bold tracking-wider">Total Faturado</div>
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">
                            {{ formatCurrency(totalEarnings) }}
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-gray-500 dark:text-gray-400 text-sm uppercase font-bold tracking-wider">Tempo Total Trabalhado</div>
                        <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-2">
                            {{ totalTime }}
                        </div>
                    </div>
                </div>

                <!-- Activity Type Summaries -->
                <div v-if="activitySummaries && activitySummaries.length > 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Resumo por Tipo de Atividade
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div
                            v-for="summary in activitySummaries"
                            :key="summary.type"
                            class="border border-gray-200 dark:border-gray-700 rounded-lg p-4"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <span :class="['text-xs font-semibold px-2.5 py-1 rounded', getActivityTypeColor(summary.type)]">
                                    {{ summary.label }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ summary.count }} registro(s)
                                </span>
                            </div>
                            <div class="mt-3">
                                <div class="text-sm text-gray-600 dark:text-gray-400">Tempo Total</div>
                                <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">
                                    {{ summary.total_time }}
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="text-sm text-gray-600 dark:text-gray-400">Valor Total</div>
                                <div class="text-xl font-bold text-green-600 dark:text-green-400 mt-1">
                                    {{ formatCurrency(summary.total_earnings) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium mb-4">Histórico Detalhado</h3>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Data</th>
                                    <th scope="col" class="px-6 py-3">Projeto</th>
                                    <th scope="col" class="px-6 py-3">Tipo</th>
                                    <th scope="col" class="px-6 py-3">Entrada</th>
                                    <th scope="col" class="px-6 py-3">Saída</th>
                                    <th scope="col" class="px-6 py-3">Duração</th>
                                    <th scope="col" class="px-6 py-3">Descrição</th>
                                    <th scope="col" class="px-6 py-3">Anexos</th>
                                    <th scope="col" class="px-6 py-3 text-right">Valor Gerado</th>
                                    <th scope="col" class="px-6 py-3 text-center">Ações</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="entry in entries" :key="entry.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">

                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ entry.date }}
                                        <span v-if="entry.manually_edited" class="ml-1 text-xs text-orange-500" title="Editado manualmente">✏️</span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                            {{ entry.project_name }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span :class="['text-xs font-medium px-2.5 py-0.5 rounded', getActivityTypeColor(entry.activity_type)]">
                                            {{ getActivityTypeLabel(entry.activity_type) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ entry.start_time }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <span v-if="entry.is_active" class="text-red-500 animate-pulse font-bold">
                                            ...
                                        </span>
                                        <span v-else>
                                            {{ entry.end_time }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 font-mono">
                                        {{ entry.duration_formatted }}
                                    </td>

                                    <td class="px-6 py-4 max-w-xs">
                                        <div v-if="editingDescription === entry.id" class="space-y-2">
                                            <textarea
                                                v-model="descriptionForm.description"
                                                class="w-full text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md resize-none"
                                                rows="2"
                                                placeholder="Descreva as atividades..."
                                            ></textarea>
                                            <div class="flex gap-1">
                                                <button
                                                    @click="saveDescription(entry.id)"
                                                    class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded"
                                                    :disabled="descriptionForm.processing"
                                                >
                                                    Salvar
                                                </button>
                                                <button
                                                    @click="cancelEdit"
                                                    class="px-2 py-1 bg-gray-500 hover:bg-gray-600 text-white text-xs rounded"
                                                >
                                                    Cancelar
                                                </button>
                                            </div>
                                        </div>
                                        <div v-else class="group flex items-start gap-2">
                                            <span class="text-xs text-gray-600 dark:text-gray-400 flex-1">
                                                {{ entry.description || 'Sem descrição' }}
                                            </span>
                                            <button
                                                @click="startEditDescription(entry.id, entry.description)"
                                                class="opacity-0 group-hover:opacity-100 text-indigo-600 hover:text-indigo-800 text-xs"
                                                title="Editar descrição"
                                            >
                                                ✏️
                                            </button>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <div v-for="(attachment, index) in entry.attachments" :key="index" class="flex items-center gap-1 text-xs">
                                                <a :href="attachment.download_url" class="text-blue-600 hover:underline flex-1 truncate max-w-[100px]">
                                                    {{ attachment.filename }}
                                                </a>
                                                <button
                                                    @click="deleteAttachment(entry.id, attachment.index)"
                                                    class="text-red-600 hover:text-red-800"
                                                    title="Remover"
                                                >
                                                    🗑️
                                                </button>
                                            </div>
                                            <input
                                                type="file"
                                                :ref="`file-${entry.id}`"
                                                @change="handleFileUpload($event, entry.id)"
                                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                                class="hidden"
                                            />
                                            <button
                                                @click="$refs[`file-${entry.id}`][0].click()"
                                                class="text-xs text-green-600 hover:text-green-800"
                                                :disabled="attachmentForm.processing"
                                            >
                                                + Anexar
                                            </button>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                                        <span v-if="!entry.is_active">
                                            {{ formatCurrency(entry.earnings) }}
                                        </span>
                                        <span v-else class="text-xs text-gray-400">
                                            calculando...
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <button
                                            v-if="!entry.is_active"
                                            @click="startManualEdit(entry)"
                                            class="text-indigo-600 hover:text-indigo-800 text-sm"
                                            title="Editar horários"
                                        >
                                            ⚙️
                                        </button>
                                    </td>
                                </tr>

                                <tr v-if="entries.length === 0">
                                    <td colspan="10" class="px-6 py-10 text-center text-gray-500">
                                        Nenhum registro encontrado. Comece a trabalhar! 💼
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal de Edição Manual -->
        <div v-if="editingEntry" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="cancelManualEdit"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Editar Registro Manualmente
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Data e Hora de Início
                                </label>
                                <input
                                    v-model="manualEditForm.start_time"
                                    type="datetime-local"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                                <div v-if="manualEditForm.errors.start_time" class="text-red-600 text-xs mt-1">
                                    {{ manualEditForm.errors.start_time }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Data e Hora de Término
                                </label>
                                <input
                                    v-model="manualEditForm.end_time"
                                    type="datetime-local"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                                <div v-if="manualEditForm.errors.end_time" class="text-red-600 text-xs mt-1">
                                    {{ manualEditForm.errors.end_time }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Activity Type
                                </label>
                                <select
                                    v-model="manualEditForm.activity_type"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="development">Development</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="meeting">Meeting</option>
                                    <option value="research">Research</option>
                                    <option value="documentation">Documentation</option>
                                    <option value="review">Review</option>
                                    <option value="support">Support</option>
                                    <option value="planning">Planning</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Descrição
                                </label>
                                <textarea
                                    v-model="manualEditForm.description"
                                    rows="3"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Descreva as atividades realizadas..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button
                            type="button"
                            @click="saveManualEdit(editingEntry)"
                            :disabled="manualEditForm.processing"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                        >
                            Salvar
                        </button>
                        <button
                            type="button"
                            @click="cancelManualEdit"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
