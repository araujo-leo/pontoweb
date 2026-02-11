<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Recuperar Senha - FreelanceTime" />

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
                <div class="mb-8 text-center sm:text-left">
                    <h1 class="text-2xl font-bold text-zinc-900">Esqueceu a senha?</h1>
                    <p class="text-zinc-500 mt-2 text-pretty leading-relaxed">
                        Não se preocupe. Informe seu e-mail e enviaremos um link para você escolher uma nova senha.
                    </p>
                </div>

                <div
                    v-if="status"
                    class="mb-6 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-100 p-4 rounded-xl flex items-start gap-3 animate-in fade-in slide-in-from-top-2 duration-500"
                >
                    <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ status }}</span>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" value="Seu e-mail de cadastro" class="text-zinc-700 font-medium" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1.5 block w-full !rounded-xl !border-zinc-200 focus:!border-emerald-500 focus:!ring-emerald-500 transition-all shadow-sm"
                            v-model="form.email"
                            placeholder="exemplo@email.com"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <button
                        class="w-full flex items-center justify-center rounded-xl bg-zinc-900 px-4 py-3.5 text-sm font-semibold text-white hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 transition-all disabled:opacity-50 shadow-lg shadow-zinc-900/10"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                    >
                        <span v-if="!form.processing">Enviar link de recuperação</span>
                        <span v-else>Enviando...</span>
                        <svg v-if="!form.processing" class="ms-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </button>
                </form>

                <div class="mt-8 text-center border-t border-zinc-100 pt-6">
                    <Link :href="route('login')" class="text-sm font-semibold text-zinc-500 hover:text-emerald-600 transition-colors inline-flex items-center gap-2 group">
                        <svg class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar para o login
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
