<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Redefinir Senha - FreelanceTime" />

    <div class="min-h-screen bg-white text-zinc-900 flex flex-col justify-center relative overflow-hidden py-12">
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-[500px] h-[500px] rounded-full bg-emerald-50 blur-3xl opacity-60"></div>
            <div class="absolute bottom-0 left-0 translate-y-1/4 -translate-x-1/4 w-[500px] h-[500px] rounded-full bg-zinc-100 blur-3xl opacity-60"></div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
            <Link href="/" class="flex justify-center items-center gap-2 mb-8 group">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-900 group-hover:bg-emerald-600 transition-colors duration-300">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold tracking-tight text-zinc-900">FreelanceTime</span>
            </Link>

            <div class="bg-white/70 backdrop-blur-xl border border-zinc-100 shadow-xl shadow-zinc-200/50 rounded-3xl p-8 sm:p-10">
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-zinc-900">Nova senha</h1>
                    <p class="text-zinc-500 mt-2 text-pretty">Quase lá! Escolha uma nova senha segura para sua conta.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <InputLabel for="email" value="Email" class="text-zinc-700 font-medium" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1.5 block w-full !rounded-xl !border-zinc-200 bg-zinc-50/50 text-zinc-500 cursor-not-allowed shadow-sm"
                            v-model="form.email"
                            required
                            readonly
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Nova Senha" class="text-zinc-700 font-medium" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                            v-model="form.password"
                            placeholder="Mínimo 8 caracteres"
                            required
                            autofocus
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Confirmar Nova Senha" class="text-zinc-700 font-medium" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                            v-model="form.password_confirmation"
                            placeholder="Repita a senha"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="pt-2">
                        <button
                            class="w-full flex items-center justify-center rounded-xl bg-zinc-900 px-4 py-3.5 text-sm font-semibold text-white hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 transition-all disabled:opacity-50 shadow-lg shadow-zinc-900/10"
                            :class="{ 'opacity-50': form.processing }"
                            :disabled="form.processing"
                        >
                            <span v-if="!form.processing">Redefinir Senha</span>
                            <span v-else>Atualizando...</span>
                            <svg v-if="!form.processing" class="ms-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center border-t border-zinc-100 pt-6">
                    <Link :href="route('login')" class="text-sm font-semibold text-zinc-500 hover:text-emerald-600 transition-colors inline-flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar para o login
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
