<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'bienvenido a mi aplicacion',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="bienvenida" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Fondo con imagen -->
        <div 
            class="relative min-h-[80vh] w-full flex items-center justify-center bg-cover bg-center bg-no-repeat before:absolute before:inset-0 before:bg-black/40 before:z-0"
            style="background-image: url('https://images.unsplash.com/photo-1497366754035-f200968a6e72?q=80&w=2069&auto=format&fit=crop');"
        >
            <!-- Mensaje de bienvenida agregado -->
            <div class="w-full text-center py-8 relative z-10 px-4">
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-white mb-4 drop-shadow-lg">
                    ¡BIENVENIDO!
                </h1>
                <p class="text-xl md:text-2xl text-white font-medium mb-6 drop-shadow-md">
                    Sistema de Gestión de Clientes y Proyectos
                </p>
                <br>
                <div class="">
                    <p class="text-lg md:text-xl text-white dark:text-gray-200 leading-relaxed">
                        Nuestro sistema de gestión está diseñado para optimizar la relación con los clientes y mejorar el 
                        control de proyectos en todas sus etapas. Con una interfaz intuitiva y herramientas avanzadas, podrás 
                        centralizar la información, automatizar procesos y tomar decisiones estratégicas basadas en datos confiables.
                    </p>
                </div>
            </div>
        </div>

        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>