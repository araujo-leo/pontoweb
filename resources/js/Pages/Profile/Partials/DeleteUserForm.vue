<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-xl font-bold text-zinc-900">
                Excluir Conta
            </h2>

            <p class="mt-2 text-sm text-zinc-500 leading-relaxed max-w-2xl">
                Uma vez que sua conta for excluída, todos os seus recursos e dados (projetos, horas e faturamento) serão removidos permanentemente.
                Por favor, baixe qualquer dado que deseje manter antes de prosseguir.
            </p>
        </header>

        <button
            @click="confirmUserDeletion"
            class="inline-flex items-center justify-center rounded-xl bg-red-50 px-5 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-100 transition-colors border border-red-100"
        >
            Excluir minha conta permanentemente
        </button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-8 bg-white">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-zinc-900">
                            Você tem certeza absoluta?
                        </h2>
                        <p class="text-sm text-zinc-500">
                            Esta ação não pode ser desfeita.
                        </p>
                    </div>
                </div>

                <p class="text-sm text-zinc-600 leading-relaxed">
                    Para confirmar, por favor insira sua senha abaixo. Isso garante que é você mesmo quem está encerrando a conta no FreelanceTime.
                </p>

                <div class="mt-6">
                    <InputLabel for="password" value="Senha" class="sr-only" />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="block w-full !rounded-xl !border-zinc-200 focus:!border-red-500 focus:!ring-red-500 transition-all shadow-sm"
                        placeholder="Sua senha atual"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-8 flex flex-col sm:flex-row justify-end gap-3">
                    <button
                        @click="closeModal"
                        class="px-5 py-2.5 text-sm font-semibold text-zinc-600 hover:bg-zinc-100 rounded-xl transition-colors"
                    >
                        Cancelar
                    </button>

                    <button
                        @click="deleteUser"
                        class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition-all shadow-lg shadow-red-600/20 disabled:opacity-50"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Confirmar Exclusão
                    </button>
                </div>
            </div>
        </Modal>
    </section>
</template>
