<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({ projects: Array });

const page = usePage();
const flashError = computed(() => page.props.errors.error);

const form = useForm({
    name: '',
    hourly_rate: '',
    description: '',
});

const timerForm = useForm({
    project_id: null,
    description: '',
    activity_type: 'development',
});

const showDescriptionInput = ref(null);

const createProject = () => {
    form.post(route('projects.store'), { onSuccess: () => form.reset() });
};

const toggleDescriptionInput = (projectId) => {
    if (showDescriptionInput.value === projectId) {
        showDescriptionInput.value = null;
        timerForm.description = '';
        timerForm.activity_type = 'development';
    } else {
        showDescriptionInput.value = projectId;
        timerForm.project_id = projectId;
        timerForm.activity_type = 'development';
    }
};

const startTimer = (projectId) => {
    timerForm.project_id = projectId;
    timerForm.post(route('time-entries.store'), {
        preserveScroll: true,
        onSuccess: () => {
            timerForm.reset();
            showDescriptionInput.value = null;
        }
    });
};

const stopTimer = (timeEntryId) => {
    router.patch(route('time-entries.update', timeEntryId), {}, { preserveScroll: true });
};

const getActiveTimer = (project) => {
    if (!project.time_entries || project.time_entries.length === 0) return null;
    return project.time_entries.find(entry => entry.end_time === null);
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};
</script>

<template>
    <Head title="Meus Projetos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <h2 class="text-2xl font-bold tracking-tight text-zinc-900">
                    Gerenciar Projetos
                </h2>
                <p class="text-sm text-zinc-500">Cadastre novos contratos e controle seu tempo de trabalho.</p>
            </div>
        </template>

        <div class="py-10 bg-zinc-50/50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                >
                    <div v-if="flashError" class="bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded-2xl flex items-center gap-3">
                        <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="text-sm font-medium">{{ flashError }}</span>
                    </div>
                </Transition>

                <div class="bg-white border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-200/60 rounded-3xl p-6 sm:p-8 transition-all">
                    <header class="mb-6">
                        <h2 class="text-lg font-bold text-zinc-900 flex items-center gap-2">
                            <div class="h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            </div>
                            Novo Projeto
                        </h2>
                    </header>

                    <form @submit.prevent="createProject" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                        <div class="md:col-span-2">
                            <InputLabel for="proj_name" value="Nome do Projeto" class="text-zinc-700" />
                            <TextInput
                                id="proj_name"
                                v-model="form.name"
                                type="text"
                                class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 shadow-sm"
                                placeholder="Ex: Redesign Website"
                                required
                            />
                        </div>
                        <div>
                            <InputLabel for="rate" value="Valor por Hora" class="text-zinc-700" />
                            <div class="relative mt-1.5">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 text-sm">R$</div>
                                <TextInput
                                    id="rate"
                                    v-model="form.hourly_rate"
                                    type="number"
                                    step="0.01"
                                    class="block w-full !pl-10 !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 shadow-sm"
                                    placeholder="0,00"
                                    required
                                />
                            </div>
                        </div>
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center rounded-xl bg-zinc-900 px-4 py-3 text-sm font-bold text-white hover:bg-zinc-800 transition-all shadow-lg shadow-zinc-900/10 h-[46px]"
                            :disabled="form.processing"
                        >
                            Salvar Projeto
                        </button>
                    </form>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="project in projects" :key="project.id"
                         class="group bg-white border-none shadow-sm shadow-zinc-200/50 ring-1 ring-zinc-200/60 rounded-3xl p-6 flex flex-col justify-between hover:ring-emerald-500/30 transition-all duration-300">

                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-bold text-zinc-900 group-hover:text-emerald-600 transition-colors">{{ project.name }}</h3>
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">
                                    {{ formatCurrency(project.hourly_rate) }}/h
                                </span>
                            </div>
                            <p class="text-sm text-zinc-500 leading-relaxed">{{ project.description || 'Sem descrição definida.' }}</p>
                        </div>

                        <div class="mt-8 pt-6 border-t border-zinc-100">
                            <div v-if="getActiveTimer(project)" class="flex flex-col gap-4">
                                <div class="flex items-center justify-between">
                                    <span class="flex items-center gap-2 text-sm font-bold text-red-600">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                        </span>
                                        Timer Ativo
                                    </span>
                                    <button
                                        @click="stopTimer(getActiveTimer(project).id)"
                                        class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700 transition-all shadow-lg shadow-red-600/20"
                                    >
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd" /></svg>
                                        Parar
                                    </button>
                                </div>
                            </div>

                            <div v-else class="space-y-4">
                                <Transition
                                    enter-active-class="transition ease-out duration-200"
                                    enter-from-class="opacity-0 -translate-y-2"
                                    enter-to-class="opacity-100 translate-y-0"
                                >
                                    <div v-if="showDescriptionInput === project.id" class="space-y-3 p-4 bg-zinc-50 rounded-2xl">
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Tipo de Atividade</label>
                                            <select
                                                v-model="timerForm.activity_type"
                                                class="w-full border-zinc-200 bg-white rounded-lg text-sm text-zinc-700 focus:ring-emerald-500 focus:border-emerald-500"
                                            >
                                                <option value="development"> Desenvolvimento</option>
                                                <option value="meeting"> Reunião</option>
                                                <option value="planning"> Planejamento</option>
                                                <option value="support"> Suporte</option>
                                            </select>
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">O que você vai fazer?</label>
                                            <textarea
                                                v-model="timerForm.description"
                                                class="w-full border-zinc-200 bg-white rounded-lg text-sm text-zinc-700 focus:ring-emerald-500 focus:border-emerald-500 resize-none"
                                                rows="2"
                                                placeholder="Ex: Ajustes no header..."
                                            ></textarea>
                                        </div>
                                    </div>
                                </Transition>

                                <div class="flex items-center justify-between gap-2">
                                    <button
                                        @click="toggleDescriptionInput(project.id)"
                                        class="h-10 w-10 flex items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 hover:bg-zinc-200 transition-colors"
                                        title="Adicionar descrição"
                                    >
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                                    </button>
                                    <button
                                        @click="startTimer(project.id)"
                                        class="flex-1 h-10 inline-flex items-center justify-center gap-2 rounded-xl bg-zinc-900 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-600 transition-all shadow-lg shadow-zinc-900/10"
                                        :disabled="timerForm.processing"
                                    >
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" /></svg>
                                        Iniciar Timer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
