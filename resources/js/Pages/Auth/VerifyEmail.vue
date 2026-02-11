<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <Head title="Verificar E-mail - FreelanceTime" />

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
                <div class="mb-8 text-center">
                    <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-emerald-50 text-emerald-600 mb-6">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                        </svg>
                    </div>

                    <h1 class="text-2xl font-bold text-zinc-900">Verifique seu e-mail</h1>
                    <p class="text-zinc-500 mt-3 text-pretty leading-relaxed">
                        Obrigado por se cadastrar! Antes de começar, por favor, confirme seu endereço de e-mail clicando no link que acabamos de enviar para você.
                    </p>
                </div>

                <div
                    v-if="verificationLinkSent"
                    class="mb-8 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-100 p-4 rounded-xl flex items-start gap-3 animate-in fade-in slide-in-from-top-2 duration-500"
                >
                    <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Um novo link de verificação foi enviado para o e-mail fornecido.</span>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <button
                        class="w-full flex items-center justify-center rounded-xl bg-zinc-900 px-4 py-3.5 text-sm font-semibold text-white hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 transition-all disabled:opacity-50 shadow-lg shadow-zinc-900/10"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                    >
                        <span v-if="!form.processing">Reenviar e-mail de verificação</span>
                        <span v-else>Enviando...</span>
                    </button>

                    <div class="flex justify-center pt-2">
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="text-sm font-semibold text-zinc-500 hover:text-zinc-900 transition-colors underline-offset-4 hover:underline"
                        >
                            Sair da conta
                        </Link>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-12 text-center">
            <p class="text-xs text-zinc-400">
                Não recebeu o e-mail? Verifique sua caixa de spam ou lixo eletrônico.
            </p>
        </div>
    </div>
</template>
