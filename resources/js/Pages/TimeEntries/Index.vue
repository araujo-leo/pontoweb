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

const startManualEdit = (entry) => {
    editingEntry.value = entry.id;
    const dateStr = entry.date.split('/').reverse().join('-');
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
        'development': 'Desenvolvimento',
        'maintenance': 'Manutenção',
        'meeting': 'Reunião',
        'research': 'Pesquisa',
        'documentation': 'Documentação',
        'review': 'Review',
        'support': 'Suporte',
        'planning': 'Planejamento'
    };
    return labels[type] || type;
};

const getActivityTypeColor = (type) => {
    const colors = {
        'development': 'bg-emerald-50 text-emerald-700',
        'maintenance': 'bg-amber-50 text-amber-700',
        'meeting': 'bg-zinc-100 text-zinc-700',
        'research': 'bg-blue-50 text-blue-700',
        'documentation': 'bg-indigo-50 text-indigo-700',
        'review': 'bg-purple-50 text-purple-700',
        'support': 'bg-rose-50 text-rose-700',
        'planning': 'bg-sky-50 text-sky-700'
    };
    return colors[type] || 'bg-zinc-50 text-zinc-700';
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};
</script>

<template>
    <Head title="Extrato de Horas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-900">Extrato de Horas</h2>
                    <p class="text-sm text-zinc-500">Analise seu tempo e exporte relatórios para seus clientes.</p>
                </div>

                <div class="flex gap-2">
                    <button @click="exportCsv" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-zinc-200 rounded-xl text-sm font-semibold text-zinc-700 hover:bg-zinc-50 transition shadow-sm">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        CSV
                    </button>
                    <button @click="exportPdf" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-900 rounded-xl text-sm font-semibold text-white hover:bg-zinc-800 transition shadow-lg shadow-zinc-900/10">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        Exportar PDF
                    </button>
                </div>
            </div>
        </template>

        <div class="py-10 bg-zinc-50/50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-200/60 rounded-3xl p-6">
                        <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider">Total Faturado</div>
                        <div class="text-3xl font-bold text-zinc-900 mt-2 flex items-baseline gap-2">
                            {{ formatCurrency(totalEarnings) }}
                            <span class="text-emerald-500 text-sm font-medium">no período</span>
                        </div>
                    </div>

                    <div class="bg-white border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-200/60 rounded-3xl p-6">
                        <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider">Tempo Trabalhado</div>
                        <div class="text-3xl font-bold text-zinc-900 mt-2">
                            {{ totalTime }}
                        </div>
                    </div>
                </div>

                <div class="bg-white border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-200/60 rounded-3xl overflow-hidden p-6">
                    <div class="flex flex-col space-y-6">
                        <div class="flex flex-wrap gap-2">
                            <button v-for="p in ['today', 'this_week', 'last_week', 'this_month', 'last_month']"
                                    :key="p"
                                    @click="setPeriod(p)"
                                    :class="[
                                    'px-4 py-2 text-sm font-medium rounded-full transition-all',
                                    filterForm.period === p
                                        ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-900/10'
                                        : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200'
                                ]"
                            >
                                {{ {today:'Hoje', this_week:'Esta Semana', last_week:'Semana Passada', this_month:'Este Mês', last_month:'Mês Passado'}[p] }}
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-zinc-100 pt-6">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-zinc-500 uppercase">Data Inicial</label>
                                <input v-model="filterForm.start_date" type="date" class="w-full rounded-xl border-zinc-200 text-sm focus:ring-emerald-500 focus:border-emerald-500" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-zinc-500 uppercase">Data Final</label>
                                <input v-model="filterForm.end_date" type="date" class="w-full rounded-xl border-zinc-200 text-sm focus:ring-emerald-500 focus:border-emerald-500" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-zinc-500 uppercase">Projeto</label>
                                <select v-model="filterForm.project_id" class="w-full rounded-xl border-zinc-200 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">Todos os Projetos</option>
                                    <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="activitySummaries?.length" class="space-y-4">
                    <h3 class="text-lg font-bold text-zinc-900">Resumo por Atividade</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div v-for="summary in activitySummaries" :key="summary.type"
                             class="bg-white p-5 rounded-2xl ring-1 ring-zinc-100 shadow-sm border-t-4 transition-transform hover:-translate-y-1"
                             :style="{ borderTopColor: 'currentColor' }"
                             :class="getActivityTypeColor(summary.type)"
                        >
                            <div class="flex justify-between items-start mb-4 text-zinc-900">
                                <span class="text-xs font-bold uppercase tracking-wider">{{ summary.label }}</span>
                                <span class="text-[10px] opacity-70 bg-white/50 px-2 py-0.5 rounded-full">{{ summary.count }} logs</span>
                            </div>
                            <div class="space-y-1 text-zinc-900">
                                <div class="text-xl font-bold">{{ summary.total_time }}</div>
                                <div class="text-sm font-medium opacity-80">{{ formatCurrency(summary.total_earnings) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-200/60 rounded-3xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-zinc-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-zinc-900">Histórico Detalhado</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] text-zinc-400 uppercase tracking-widest bg-zinc-50/50">
                            <tr>
                                <th class="px-6 py-4">Data/Projeto</th>
                                <th class="px-6 py-4">Tipo</th>
                                <th class="px-6 py-4">Horário/Duração</th>
                                <th class="px-6 py-4">Descrição/Anexos</th>
                                <th class="px-6 py-4 text-right">Valor</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                            <tr v-for="entry in entries" :key="entry.id" class="group hover:bg-zinc-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-zinc-900">{{ entry.date }}</div>
                                    <div class="text-[10px] font-bold text-emerald-600 mt-1 uppercase">{{ entry.project_name }}</div>
                                </td>

                                <td class="px-6 py-4">
                                        <span :class="['text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-tighter', getActivityTypeColor(entry.activity_type)]">
                                            {{ getActivityTypeLabel(entry.activity_type) }}
                                        </span>
                                </td>

                                <td class="px-6 py-4 font-mono text-zinc-600">
                                    <div class="text-xs">{{ entry.start_time }} — {{ entry.is_active ? 'Ativo' : entry.end_time }}</div>
                                    <div class="text-sm font-bold text-zinc-900">{{ entry.duration_formatted }}</div>
                                </td>

                                <td class="px-6 py-4 max-w-xs">
                                    <div class="relative">
                                        <p class="text-xs text-zinc-500 leading-relaxed italic">"{{ entry.description || 'Sem descrição' }}"</p>

                                        <div class="flex flex-wrap gap-2 mt-2">
                                            <div v-for="(file, i) in entry.attachments" :key="i" class="flex items-center gap-1 bg-zinc-100 px-1.5 py-0.5 rounded text-[10px]">
                                                <a :href="file.download_url" class="truncate max-w-[80px] hover:text-emerald-600">{{ file.filename }}</a>
                                                <button @click="deleteAttachment(entry.id, file.index)" class="text-zinc-400 hover:text-red-500">×</button>
                                            </div>
                                            <input type="file" :ref="`file-${entry.id}`" @change="handleFileUpload($event, entry.id)" class="hidden" />
                                            <button @click="$refs[`file-${entry.id}`][0].click()" class="text-emerald-600 font-bold text-[10px] hover:underline">+ Anexo</button>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="text-sm font-bold text-zinc-900">{{ entry.is_active ? '--' : formatCurrency(entry.earnings) }}</div>
                                    <div v-if="entry.manually_edited" class="text-[9px] text-amber-500 font-bold uppercase">Editado manual</div>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <button v-if="!entry.is_active" @click="startManualEdit(entry)" class="p-2 text-zinc-400 hover:text-zinc-900 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="entries.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-zinc-400 italic">Nenhum registro encontrado para este filtro.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <Transition name="fade">
            <div v-if="editingEntry" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-zinc-900/40 backdrop-blur-sm" @click="cancelManualEdit"></div>
                <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-in zoom-in duration-300">
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-zinc-900 mb-6">Editar Registro</h3>
                        <div class="space-y-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-zinc-500 uppercase">Início</label>
                                    <input v-model="manualEditForm.start_time" type="datetime-local" class="w-full rounded-xl border-zinc-200 text-sm" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-zinc-500 uppercase">Fim</label>
                                    <input v-model="manualEditForm.end_time" type="datetime-local" class="w-full rounded-xl border-zinc-200 text-sm" />
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-zinc-500 uppercase">Tipo</label>
                                <select v-model="manualEditForm.activity_type" class="w-full rounded-xl border-zinc-200 text-sm">
                                    <option value="development">🔧 Desenvolvimento</option>
                                    <option value="maintenance">🛠️ Manutenção</option>
                                    <option value="meeting">👥 Reunião</option>
                                    <option value="research">🔍 Pesquisa</option>
                                    <option value="documentation">📝 Documentação</option>
                                    <option value="review">✅ Review</option>
                                    <option value="support">💬 Suporte</option>
                                    <option value="planning">📋 Planejamento</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-zinc-500 uppercase">Descrição</label>
                                <textarea v-model="manualEditForm.description" class="w-full rounded-xl border-zinc-200 text-sm" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-8">
                            <button @click="saveManualEdit(editingEntry)" class="flex-1 bg-zinc-900 text-white font-bold py-3 rounded-xl hover:bg-zinc-800 transition">Salvar</button>
                            <button @click="cancelManualEdit" class="px-6 text-zinc-500 font-bold hover:text-zinc-900 transition">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
