<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const mobileMenuOpen = ref(false);

const features = [
    {
        icon: 'projects',
        title: 'Gestão de Projetos',
        description: 'Cadastre seus projetos, defina orçamentos, prazos e acompanhe o progresso de cada um em tempo real.'
    },
    {
        icon: 'clock',
        title: 'Controle de Horas',
        description: 'Registre suas horas trabalhadas com precisão. Bata ponto de forma simples e tenha controle total do seu tempo.'
    },
    {
        icon: 'calculator',
        title: 'Cálculo Automático',
        description: 'Calcule automaticamente seus ganhos por projeto, por hora ou por período. Visualize relatórios detalhados.'
    },
    {
        icon: 'report',
        title: 'Relatórios Inteligentes',
        description: 'Gere relatórios profissionais para seus clientes e tenha insights sobre sua produtividade.'
    }
];

const stats = [
    { value: '50k+', label: 'Freelancers ativos' },
    { value: '2M+', label: 'Horas registradas' },
    { value: '98%', label: 'Satisfação' },
    { value: 'R$10M+', label: 'Faturados' }
];

const testimonials = [
    {
        name: 'Marina Silva',
        role: 'Designer Freelancer',
        content: 'O FreelanceTime mudou completamente a forma como gerencio meus projetos. Agora sei exatamente quanto tempo dedico a cada cliente.',
        avatar: 'MS'
    },
    {
        name: 'Carlos Eduardo',
        role: 'Desenvolvedor Web',
        content: 'Finalmente consigo cobrar de forma justa pelo meu trabalho. Os relatórios automáticos impressionam meus clientes.',
        avatar: 'CE'
    },
    {
        name: 'Ana Beatriz',
        role: 'Redatora',
        content: 'Interface simples e intuitiva. Em minutos já estava usando e registrando minhas horas de trabalho.',
        avatar: 'AB'
    }
];


</script>

<template>
    <Head title="FreelanceTime - Controle de Tempo para Freelancers" />

    <div class="min-h-screen bg-white text-zinc-900">
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-zinc-100">
            <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center gap-2">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-zinc-900">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight">FreelanceTime</span>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex md:items-center md:gap-8">
                        <a href="#features" class="text-sm font-medium text-zinc-600 hover:text-zinc-900 transition">Recursos</a>
                        <a href="#how-it-works" class="text-sm font-medium text-zinc-600 hover:text-zinc-900 transition">Como funciona</a>
                        <a href="#testimonials" class="text-sm font-medium text-zinc-600 hover:text-zinc-900 transition">Depoimentos</a>
                    </div>

                    <!-- Auth Buttons -->
                    <div v-if="canLogin" class="hidden md:flex md:items-center md:gap-4">
                        <Link
                            v-if="$page.props.auth?.user"
                            :href="route('dashboard')"
                            class="text-sm font-medium text-zinc-600 hover:text-zinc-900 transition"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="text-sm font-medium text-zinc-600 hover:text-zinc-900 transition"
                            >
                                Entrar
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="inline-flex items-center justify-center rounded-full bg-zinc-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-zinc-800 transition"
                            >
                                Começar grátis
                            </Link>
                        </template>
                    </div>

                    <!-- Mobile menu button -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden inline-flex items-center justify-center rounded-lg p-2 text-zinc-600 hover:bg-zinc-100"
                    >
                        <svg v-if="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile menu -->
                <div v-show="mobileMenuOpen" class="md:hidden border-t border-zinc-100 py-4">
                    <div class="flex flex-col gap-4">
                        <a href="#features" class="text-sm font-medium text-zinc-600 hover:text-zinc-900">Recursos</a>
                        <a href="#how-it-works" class="text-sm font-medium text-zinc-600 hover:text-zinc-900">Como funciona</a>
                        <a href="#testimonials" class="text-sm font-medium text-zinc-600 hover:text-zinc-900">Depoimentos</a>
                        <div v-if="canLogin" class="flex flex-col gap-2 pt-4 border-t border-zinc-100">
                            <Link
                                v-if="$page.props.auth?.user"
                                :href="route('dashboard')"
                                class="text-sm font-medium text-zinc-900"
                            >
                                Dashboard
                            </Link>
                            <template v-else>
                                <Link :href="route('login')" class="text-sm font-medium text-zinc-600">Entrar</Link>
                                <Link
                                    v-if="canRegister"
                                    :href="route('register')"
                                    class="inline-flex items-center justify-center rounded-full bg-zinc-900 px-5 py-2.5 text-sm font-medium text-white"
                                >
                                    Começar grátis
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-32 overflow-hidden">
            <div class="absolute inset-0 -z-10">
                <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-[600px] h-[600px] rounded-full bg-emerald-50 blur-3xl opacity-60"></div>
                <div class="absolute bottom-0 left-0 translate-y-1/4 -translate-x-1/4 w-[600px] h-[600px] rounded-full bg-zinc-100 blur-3xl opacity-60"></div>
            </div>

            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-4xl mx-auto">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-1.5 text-sm font-medium text-emerald-700 mb-8">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Novo: Timer inteligente com IA
                    </div>

                    <!-- Headline -->
                    <h1 class="text-4xl sm:text-5xl lg:text-7xl font-bold tracking-tight text-zinc-900 text-balance">
                        Controle total do seu
                        <span class="text-emerald-600">tempo e projetos</span>
                    </h1>

                    <!-- Subheadline -->
                    <p class="mt-6 text-lg sm:text-xl text-zinc-600 max-w-2xl mx-auto text-pretty">
                        A plataforma completa para freelancers gerenciarem projetos, registrarem horas e calcularem ganhos de forma automática.
                    </p>

                    <!-- CTAs -->
                    <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="w-full sm:w-auto inline-flex items-center justify-center rounded-full bg-zinc-900 px-8 py-4 text-base font-medium text-white hover:bg-zinc-800 transition shadow-lg shadow-zinc-900/20"
                        >
                            Começar grátis
                            <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </Link>
                        <a
                            href="#features"
                            class="w-full sm:w-auto inline-flex items-center justify-center rounded-full border border-zinc-200 bg-white px-8 py-4 text-base font-medium text-zinc-900 hover:bg-zinc-50 transition"
                        >
                            Ver como funciona
                        </a>
                    </div>

                    <!-- Social Proof -->
                    <p class="mt-8 text-sm text-zinc-500">
                        Mais de <span class="font-semibold text-zinc-700">50.000 freelancers</span> já usam o FreelanceTime
                    </p>
                </div>

                <!-- Hero Image / App Preview -->
                <div class="mt-16 lg:mt-20">
                    <div class="relative mx-auto max-w-5xl">
                        <div class="rounded-2xl bg-zinc-900 p-2 shadow-2xl shadow-zinc-900/20">
                            <div class="rounded-xl bg-zinc-800 overflow-hidden">
                                <!-- Browser Bar -->
                                <div class="flex items-center gap-2 px-4 py-3 border-b border-zinc-700">
                                    <div class="flex gap-1.5">
                                        <div class="h-3 w-3 rounded-full bg-zinc-600"></div>
                                        <div class="h-3 w-3 rounded-full bg-zinc-600"></div>
                                        <div class="h-3 w-3 rounded-full bg-zinc-600"></div>
                                    </div>
                                    <div class="flex-1 flex justify-center">
                                        <div class="flex items-center gap-2 rounded-lg bg-zinc-700 px-4 py-1.5 text-xs text-zinc-400">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                            </svg>
                                            app.freelancetime.com.br
                                        </div>
                                    </div>
                                </div>

                                <!-- App Preview Content -->
                                <div class="p-6 bg-zinc-50">
                                    <div class="grid grid-cols-12 gap-4">
                                        <!-- Sidebar -->
                                        <div class="col-span-3 hidden lg:block">
                                            <div class="space-y-2">
                                                <div class="flex items-center gap-3 rounded-lg bg-emerald-100 px-3 py-2">
                                                    <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                                                    <div class="h-2 w-20 rounded bg-emerald-300"></div>
                                                </div>
                                                <div class="flex items-center gap-3 rounded-lg px-3 py-2">
                                                    <div class="h-2 w-2 rounded-full bg-zinc-300"></div>
                                                    <div class="h-2 w-16 rounded bg-zinc-200"></div>
                                                </div>
                                                <div class="flex items-center gap-3 rounded-lg px-3 py-2">
                                                    <div class="h-2 w-2 rounded-full bg-zinc-300"></div>
                                                    <div class="h-2 w-24 rounded bg-zinc-200"></div>
                                                </div>
                                                <div class="flex items-center gap-3 rounded-lg px-3 py-2">
                                                    <div class="h-2 w-2 rounded-full bg-zinc-300"></div>
                                                    <div class="h-2 w-14 rounded bg-zinc-200"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Main Content -->
                                        <div class="col-span-12 lg:col-span-9 space-y-4">
                                            <!-- Stats Row -->
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                                <div class="rounded-xl bg-white p-4 shadow-sm">
                                                    <div class="text-xs text-zinc-500">Horas hoje</div>
                                                    <div class="mt-1 text-2xl font-bold text-zinc-900">6h 32m</div>
                                                </div>
                                                <div class="rounded-xl bg-white p-4 shadow-sm">
                                                    <div class="text-xs text-zinc-500">Esta semana</div>
                                                    <div class="mt-1 text-2xl font-bold text-zinc-900">38h 15m</div>
                                                </div>
                                                <div class="rounded-xl bg-white p-4 shadow-sm">
                                                    <div class="text-xs text-zinc-500">Projetos ativos</div>
                                                    <div class="mt-1 text-2xl font-bold text-zinc-900">5</div>
                                                </div>
                                                <div class="rounded-xl bg-white p-4 shadow-sm">
                                                    <div class="text-xs text-zinc-500">Ganhos do mês</div>
                                                    <div class="mt-1 text-2xl font-bold text-emerald-600">R$ 8.450</div>
                                                </div>
                                            </div>

                                            <!-- Timer Card -->
                                            <div class="rounded-xl bg-white p-6 shadow-sm">
                                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                                    <div>
                                                        <div class="text-sm text-zinc-500">Trabalhando em</div>
                                                        <div class="text-lg font-semibold text-zinc-900">Website Redesign - Cliente ABC</div>
                                                    </div>
                                                    <div class="flex items-center gap-4">
                                                        <div class="text-3xl font-mono font-bold text-zinc-900">02:34:18</div>
                                                        <button class="h-12 w-12 rounded-full bg-red-500 flex items-center justify-center text-white shadow-lg shadow-red-500/30">
                                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Projects List -->
                                            <div class="rounded-xl bg-white p-4 shadow-sm">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="text-sm font-medium text-zinc-900">Projetos Recentes</div>
                                                    <div class="text-xs text-emerald-600 font-medium">Ver todos</div>
                                                </div>
                                                <div class="space-y-3">
                                                    <div class="flex items-center justify-between py-2 border-b border-zinc-100">
                                                        <div class="flex items-center gap-3">
                                                            <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold">AB</div>
                                                            <div>
                                                                <div class="text-sm font-medium text-zinc-900">App Mobile</div>
                                                                <div class="text-xs text-zinc-500">24h / 40h estimadas</div>
                                                            </div>
                                                        </div>
                                                        <div class="text-sm font-medium text-zinc-900">R$ 3.200</div>
                                                    </div>
                                                    <div class="flex items-center justify-between py-2">
                                                        <div class="flex items-center gap-3">
                                                            <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 text-xs font-bold">WR</div>
                                                            <div>
                                                                <div class="text-sm font-medium text-zinc-900">Website Redesign</div>
                                                                <div class="text-xs text-zinc-500">18h / 30h estimadas</div>
                                                            </div>
                                                        </div>
                                                        <div class="text-sm font-medium text-zinc-900">R$ 2.400</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-16 border-y border-zinc-100 bg-zinc-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                    <div v-for="stat in stats" :key="stat.label" class="text-center">
                        <div class="text-3xl sm:text-4xl font-bold text-zinc-900">{{ stat.value }}</div>
                        <div class="mt-1 text-sm text-zinc-600">{{ stat.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 lg:py-32">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-zinc-900 text-balance">
                        Tudo que você precisa para gerenciar seu trabalho freelancer
                    </h2>
                    <p class="mt-4 text-lg text-zinc-600 text-pretty">
                        Ferramentas poderosas e intuitivas para você focar no que realmente importa: seu trabalho.
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div
                        v-for="feature in features"
                        :key="feature.title"
                        class="group relative p-6 rounded-2xl border border-zinc-200 bg-white hover:border-zinc-300 hover:shadow-lg transition-all duration-300"
                    >
                        <div class="h-12 w-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 mb-5 group-hover:scale-110 transition-transform">
                            <!-- Projects Icon -->
                            <svg v-if="feature.icon === 'projects'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <!-- Clock Icon -->
                            <svg v-else-if="feature.icon === 'clock'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <!-- Calculator Icon -->
                            <svg v-else-if="feature.icon === 'calculator'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <!-- Report Icon -->
                            <svg v-else-if="feature.icon === 'report'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 mb-2">{{ feature.title }}</h3>
                        <p class="text-sm text-zinc-600 leading-relaxed">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it Works Section -->
        <section id="how-it-works" class="py-20 lg:py-32 bg-zinc-900 text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-balance">
                        Como funciona
                    </h2>
                    <p class="mt-4 text-lg text-zinc-400 text-pretty">
                        Em 3 passos simples você já está no controle do seu tempo e projetos.
                    </p>
                </div>

                <div class="grid lg:grid-cols-3 gap-8">
                    <div class="relative p-8 rounded-2xl bg-zinc-800/50 border border-zinc-700">
                        <div class="absolute -top-4 left-8 h-8 w-8 rounded-full bg-emerald-500 flex items-center justify-center text-sm font-bold text-white">1</div>
                        <h3 class="text-xl font-semibold mt-4 mb-3">Cadastre seus projetos</h3>
                        <p class="text-zinc-400">Adicione seus projetos com informações de cliente, valor por hora e estimativa de tempo.</p>
                    </div>
                    <div class="relative p-8 rounded-2xl bg-zinc-800/50 border border-zinc-700">
                        <div class="absolute -top-4 left-8 h-8 w-8 rounded-full bg-emerald-500 flex items-center justify-center text-sm font-bold text-white">2</div>
                        <h3 class="text-xl font-semibold mt-4 mb-3">Bata ponto com um clique</h3>
                        <p class="text-zinc-400">Inicie e pause o timer sempre que começar ou terminar de trabalhar. Simples assim.</p>
                    </div>
                    <div class="relative p-8 rounded-2xl bg-zinc-800/50 border border-zinc-700">
                        <div class="absolute -top-4 left-8 h-8 w-8 rounded-full bg-emerald-500 flex items-center justify-center text-sm font-bold text-white">3</div>
                        <h3 class="text-xl font-semibold mt-4 mb-3">Receba seus relatórios</h3>
                        <p class="text-zinc-400">Visualize seus ganhos, horas trabalhadas e gere relatórios profissionais para seus clientes.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials" class="py-20 lg:py-32">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-zinc-900 text-balance">
                        Amado por freelancers de todo o Brasil
                    </h2>
                    <p class="mt-4 text-lg text-zinc-600 text-pretty">
                        Veja o que nossos usuários estão falando sobre o FreelanceTime.
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="testimonial in testimonials"
                        :key="testimonial.name"
                        class="p-6 rounded-2xl border border-zinc-200 bg-white"
                    >
                        <div class="flex items-center gap-1 mb-4">
                            <svg v-for="i in 5" :key="i" class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <p class="text-zinc-600 mb-6">{{ testimonial.content }}</p>
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-zinc-200 flex items-center justify-center text-sm font-bold text-zinc-600">
                                {{ testimonial.avatar }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-zinc-900">{{ testimonial.name }}</div>
                                <div class="text-xs text-zinc-500">{{ testimonial.role }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 lg:py-32">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-3xl bg-zinc-900 px-8 py-16 sm:px-16 lg:py-24">
                    <div class="absolute inset-0 -z-10">
                        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-[500px] h-[500px] rounded-full bg-emerald-500/20 blur-3xl"></div>
                        <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/2 w-[500px] h-[500px] rounded-full bg-zinc-700 blur-3xl"></div>
                    </div>

                    <div class="relative text-center max-w-2xl mx-auto">
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-white text-balance">
                            Pronto para ter controle total do seu tempo?
                        </h2>
                        <p class="mt-4 text-lg text-zinc-400 text-pretty">
                            Junte-se a milhares de freelancers que já transformaram a forma de gerenciar seus projetos.
                        </p>
                        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="w-full sm:w-auto inline-flex items-center justify-center rounded-full bg-white px-8 py-4 text-base font-medium text-zinc-900 hover:bg-zinc-100 transition"
                            >
                                Criar conta grátis
                                <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-zinc-200 py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
                    <div class="col-span-2 md:col-span-1">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-900">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
                                </svg>
                            </div>
                            <span class="text-lg font-bold">FreelanceTime</span>
                        </div>
                        <p class="text-sm text-zinc-500">A plataforma completa para freelancers gerenciarem seu tempo e projetos.</p>
                    </div>

                    <div>
                        <div class="text-sm font-semibold text-zinc-900 mb-4">Produto</div>
                        <ul class="space-y-3">
                            <li><a href="#features" class="text-sm text-zinc-500 hover:text-zinc-900 transition">Recursos</a></li>
                            <li><a href="#how-it-works" class="text-sm text-zinc-500 hover:text-zinc-900 transition">Como funciona</a></li>
                            <li><a href="#testimonials" class="text-sm text-zinc-500 hover:text-zinc-900 transition">Depoimentos</a></li>
                        </ul>
                    </div>

                    <div>
                        <div class="text-sm font-semibold text-zinc-900 mb-4">Empresa</div>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-sm text-zinc-500 hover:text-zinc-900 transition">Sobre nós</a></li>
                            <li><a href="#" class="text-sm text-zinc-500 hover:text-zinc-900 transition">Blog</a></li>
                            <li><a href="#" class="text-sm text-zinc-500 hover:text-zinc-900 transition">Carreiras</a></li>
                            <li><a href="#" class="text-sm text-zinc-500 hover:text-zinc-900 transition">Contato</a></li>
                        </ul>
                    </div>

                    <div>
                        <div class="text-sm font-semibold text-zinc-900 mb-4">Legal</div>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-sm text-zinc-500 hover:text-zinc-900 transition">Privacidade</a></li>
                            <li><a href="#" class="text-sm text-zinc-500 hover:text-zinc-900 transition">Termos</a></li>
                            <li><a href="#" class="text-sm text-zinc-500 hover:text-zinc-900 transition">Cookies</a></li>
                        </ul>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between pt-8 border-t border-zinc-200 gap-4">
                    <p class="text-sm text-zinc-500">&copy; 2026 FreelanceTime. Todos os direitos reservados.</p>
                    <div class="flex items-center gap-4">
                        <a href="#" class="text-zinc-400 hover:text-zinc-600 transition">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>
                        <a href="#" class="text-zinc-400 hover:text-zinc-600 transition">
                            <span class="sr-only">GitHub</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-zinc-400 hover:text-zinc-600 transition">
                            <span class="sr-only">LinkedIn</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg>
                        </a>
                        <a href="#" class="text-zinc-400 hover:text-zinc-600 transition">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
html {
    scroll-behavior: smooth;
}
</style>
