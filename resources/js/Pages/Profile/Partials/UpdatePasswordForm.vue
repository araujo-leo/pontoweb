<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-xl font-bold text-zinc-900">
                Atualizar Senha
            </h2>

            <p class="mt-2 text-sm text-zinc-500 leading-relaxed max-w-2xl">
                Certifique-se de que sua conta esteja usando uma senha longa e aleatória para manter a segurança dos seus dados de faturamento.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-8 space-y-6 max-w-xl">
            <div>
                <InputLabel for="current_password" value="Senha Atual" class="text-zinc-700 font-medium" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                    autocomplete="current-password"
                    placeholder="••••••••"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="password" value="Nova Senha" class="text-zinc-700 font-medium" />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                        autocomplete="new-password"
                        placeholder="Mín. 8 caracteres"
                    />
                </div>

                <div>
                    <InputLabel
                        for="password_confirmation"
                        value="Confirmar Nova Senha"
                        class="text-zinc-700 font-medium"
                    />

                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                        autocomplete="new-password"
                        placeholder="Repita a senha"
                    />
                </div>

                <InputError :message="form.errors.password" class="col-span-full -mt-4" />
                <InputError :message="form.errors.password_confirmation" class="col-span-full -mt-4" />
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-zinc-900 px-6 py-3 text-sm font-semibold text-white hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 transition-all shadow-lg shadow-zinc-900/10 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    Salvar Alterações
                </button>

                <Transition
                    enter-active-class="transition ease-in-out duration-300"
                    enter-from-class="opacity-0 translate-x-2"
                    enter-to-class="opacity-100 translate-x-0"
                    leave-active-class="transition ease-in-out duration-300"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm font-medium text-emerald-600 flex items-center gap-1"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Senha atualizada!
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
