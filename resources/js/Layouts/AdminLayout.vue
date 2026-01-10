<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="min-h-screen bg-[#0B132B]">

        <!-- NAVBAR -->
        <nav class="bg-[#F5F5DC] border-b border-[#D4AF37] shadow-md">
            <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

                <!-- TÍTULO -->
                <h1 class="text-xl font-bold text-[#0B132B]">
                    Panel Administrativo
                </h1>

                <!-- MENÚ -->
                <div class="flex gap-6 text-[#0B132B] font-semibold">

                    <Link href="/admin/categorias-interes" class="hover:text-[#D4AF37]">
                        Categorías
                    </Link>

                    <Link href="/admin/opciones-interes" class="hover:text-[#D4AF37]">
                        Opciones
                    </Link>

                    

                    
                    <Link 
                        href="/logout"
                        method="post"
                        as="button"
                        class="hover:text-red-600"
                    >
                        Cerrar Sesión
                    </Link>

                </div>
            </div>
        </nav>

        // CONTENIDO 
        <main
            class="max-w-5xl mx-auto mt-12 p-8 bg-[#F5F5DC] border border-[#D4AF37]
                   shadow-lg rounded-lg text-[#0B132B]"
        >
            <slot />
        </main>
    </div>
</template>
