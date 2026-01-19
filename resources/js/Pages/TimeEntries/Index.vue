<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    entries: Array,
    totalEarnings: Number,
    totalTime: String,
});

const editingDescription = ref(null);
const descriptionForm = useForm({
    description: '',
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

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium mb-4">Histórico Detalhado</h3>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Data</th>
                                    <th scope="col" class="px-6 py-3">Projeto</th>
                                    <th scope="col" class="px-6 py-3">Entrada</th>
                                    <th scope="col" class="px-6 py-3">Saída</th>
                                    <th scope="col" class="px-6 py-3">Duração</th>
                                    <th scope="col" class="px-6 py-3">Descrição</th>
                                    <th scope="col" class="px-6 py-3 text-right">Valor Gerado</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="entry in entries" :key="entry.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">

                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ entry.date }}
                                    </td>

                                    <td class="px-6 py-4">
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                                {{ entry.project_name }}
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
                                        {{ entry.duration }}
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

                                    <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                                            <span v-if="!entry.is_active">
                                                {{ formatCurrency(entry.earnings) }}
                                            </span>
                                        <span v-else class="text-xs text-gray-400">
                                                calculando...
                                            </span>
                                    </td>
                                </tr>

                                <tr v-if="entries.length === 0">
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-500">
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
    </AuthenticatedLayout>
</template>
