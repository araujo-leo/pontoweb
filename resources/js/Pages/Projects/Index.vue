<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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
});

const showDescriptionInput = ref(null);

const createProject = () => {
    form.post(route('projects.store'), { onSuccess: () => form.reset() });
};

const toggleDescriptionInput = (projectId) => {
    if (showDescriptionInput.value === projectId) {
        showDescriptionInput.value = null;
        timerForm.description = '';
    } else {
        showDescriptionInput.value = projectId;
        timerForm.project_id = projectId;
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
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gerenciar Projetos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <div v-if="flashError" class="bg-red-100 text-red-700 px-4 py-3 rounded relative">
                    {{ flashError }}
                </div>

                <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Novo Projeto</h2>
                    </header>
                    <form @submit.prevent="createProject" class="mt-4 flex flex-col md:flex-row gap-4 items-end">
                        <div class="w-full">
                            <label class="text-sm text-gray-700 dark:text-gray-300">Nome</label>
                            <input v-model="form.name" type="text" class="w-full border-gray-300 rounded-md dark:bg-gray-900 dark:text-white" required />
                        </div>
                        <div class="w-full md:w-1/3">
                            <label class="text-sm text-gray-700 dark:text-gray-300">Valor/Hora</label>
                            <input v-model="form.hourly_rate" type="number" step="0.01" class="w-full border-gray-300 rounded-md dark:bg-gray-900 dark:text-white" required />
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md h-10 hover:bg-gray-700" :disabled="form.processing">
                            Salvar
                        </button>
                    </form>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="project in projects" :key="project.id" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ project.name }}</h3>
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">{{ formatCurrency(project.hourly_rate) }}/h</span>
                            </div>
                            <p class="text-sm text-gray-500">{{ project.description }}</p>
                        </div>

                        <div class="mt-6 border-t pt-4 dark:border-gray-700">
                            <div v-if="getActiveTimer(project)" class="flex items-center justify-between">
                                <span class="text-red-500 animate-pulse font-bold text-sm flex items-center">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span> Em andamento
                                </span>
                                <button @click="stopTimer(getActiveTimer(project).id)" class="bg-red-500 hover:bg-red-700 text-white text-sm font-bold py-2 px-4 rounded">
                                    Parar
                                </button>
                            </div>
                            <div v-else>
                                <div v-if="showDescriptionInput === project.id" class="space-y-2 mb-3">
                                    <label class="text-xs text-gray-600 dark:text-gray-400">Descrição das atividades (opcional)</label>
                                    <textarea
                                        v-model="timerForm.description"
                                        placeholder="Descreva o que você vai fazer..."
                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-sm resize-none"
                                        rows="2"
                                    ></textarea>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button
                                        v-if="showDescriptionInput !== project.id"
                                        @click="toggleDescriptionInput(project.id)"
                                        class="bg-gray-500 hover:bg-gray-600 text-white text-sm font-bold py-2 px-4 rounded"
                                    >
                                        + Descrição
                                    </button>
                                    <button
                                        @click="startTimer(project.id)"
                                        class="bg-indigo-600 hover:bg-indigo-800 text-white text-sm font-bold py-2 px-4 rounded flex items-center"
                                        :disabled="timerForm.processing"
                                    >
                                        ▶ Iniciar
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
