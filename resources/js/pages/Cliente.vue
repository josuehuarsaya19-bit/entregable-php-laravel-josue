<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { FileText, Pencil, Plus, Save, SquareX, Trash2 } from 'lucide-vue-next';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';

const clientes = ref([]);
const miNombre = ref('');
const mostrarModal = ref(false);
const mostrarModalEditar = ref(false);
const idCliente = ref('');

//Formulario
const formulario = ref({
    nombre: '',
    email: '',
    telefono: '',
    direccion: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Clientes',
        href: dashboard().url,
    },
];

const listarCliente = async () => {
    try {
        const respuesta = await axios.get('/clientes/listar');
        console.log(respuesta);
        if (respuesta.data.success) {
            clientes.value = respuesta.data.data;
            miNombre.value = respuesta.data.nombre;
        }
    } catch (error: any) {
        console.error('Error al listar clientes:', error);
    }
};

const abrirModal = () => {
    mostrarModal.value = true;
    // Limpiar formulario al abrir modal
    formulario.value = {
        nombre: '',
        email: '',
        telefono: '',
        direccion: '',
    };
};

const cerrarModal = () => {
    mostrarModal.value = false;
};

//funciones para manipular el modal editar
const abrirModalEditar = (dataCliente: any) => {
    mostrarModalEditar.value = true;
    idCliente.value = dataCliente.id;

    console.log('Datos cliente:', dataCliente);

    formulario.value.nombre = dataCliente.nombre;
    formulario.value.email = dataCliente.email;
    formulario.value.telefono = dataCliente.telefono || '';
    formulario.value.direccion = dataCliente.direccion || '';
};

const cerrarModalEditar = () => {
    mostrarModalEditar.value = false;
};

const enviarFormulario = async () => {
    console.log('Enviando formulario cliente');
    console.log(formulario.value);

    try {
        const respuesta = await axios.post('/clientes/guardar', formulario.value);
        if (respuesta.data.success) {
            Swal.fire({
                title: 'Recurso Creado',
                text: 'Cliente Creado',
                icon: 'success',
            });
            mostrarModal.value = false;
            listarCliente();
        } else {
            Swal.fire({
                title: 'Error al crear',
                text: 'Cliente no creado',
                icon: 'error',
            });
        }
    } catch (error: any) {
        let errorMessage = 'Error al crear cliente';
        if (error.response && error.response.data && error.response.data.data) {
            errorMessage = error.response.data.data;
        }
        Swal.fire({
            title: 'Error',
            text: errorMessage,
            icon: 'error',
        });
    }
};

const actualizarFormulario = async () => {
    console.log('Actualizando cliente');
    console.log(formulario.value);

    try {
        const respuesta = await axios.put(`/clientes/editar/${idCliente.value}`, formulario.value);
        if (respuesta.data.success) {
            Swal.fire({
                title: 'Recurso Actualizado',
                text: 'Cliente Actualizado',
                icon: 'success',
            });
            mostrarModalEditar.value = false;
            listarCliente();
        }
    } catch (error: any) {
        let errorMessage = 'Error al actualizar cliente';
        if (error.response && error.response.data && error.response.data.data) {
            errorMessage = error.response.data.data;
        }
        Swal.fire({
            title: 'Error',
            text: errorMessage,
            icon: 'error',
        });
    }
};

const exportarPdf = () => {
    const url = '/clientes/exportar-pdf';
    window.location.href = url;
};

const eliminarCliente = async (id: number) => {
    try {
        const respuesta = await axios.delete(`/clientes/eliminar/${id}`);
        console.log(respuesta);
        if (respuesta.data.success) {
            Swal.fire({
                title: 'Recurso Eliminado',
                text: 'Cliente Eliminado',
                icon: 'success',
            });
        }
        listarCliente();
    } catch (error: any) {
        Swal.fire({
            title: 'Error',
            text: 'No se pudo eliminar el cliente',
            icon: 'error',
        });
    }
};

const confirmacion = (id: number) => {
    Swal.fire({
        title: '¿Estás Seguro?',
        text: 'El cliente se eliminará de forma permanente',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarCliente(id);
        }
    });
};

onMounted(listarCliente);
</script>

<template>
    <Head title="Clientes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col items-center justify-center">
            <div class="mt-4">
                <p class="text-2xl text-amber-600">Gestión de Clientes 😊</p>
                <small>{{ miNombre }}</small>
            </div>
            <div>
                <!-- Modal registro cliente -->
                <div
                    class="fixed inset-0 z-50 grid place-content-center bg-black/50 p-4"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="modalTitle"
                    v-if="mostrarModal"
                >
                    <div
                        class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-gray-900"
                    >
                        <h2
                            id="modalTitle"
                            class="text-xl font-bold text-gray-900 sm:text-2xl dark:text-white"
                        >
                            Registro de Cliente
                        </h2>

                        <form class="mt-4" @submit.prevent="enviarFormulario">
                            <div class="mb-3">
                                <label for="nombre">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                    >
                                        Nombre *
                                    </span>
                                    <input
                                        type="text"
                                        id="nombre"
                                        v-model="formulario.nombre"
                                        required
                                        class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    />
                                </label>
                            </div>
                            <div class="mb-3">
                                <label for="email">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                    >
                                        Email *
                                    </span>
                                    <input
                                        type="email"
                                        id="email"
                                        v-model="formulario.email"
                                        required
                                        class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    />
                                </label>
                            </div>
                            <div class="mb-3">
                                <label for="telefono">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                    >
                                        Teléfono
                                    </span>
                                    <input
                                        type="text"
                                        id="telefono"
                                        v-model="formulario.telefono"
                                        class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    />
                                </label>
                            </div>
                            <div class="mb-3">
                                <label for="direccion">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                    >
                                        Dirección
                                    </span>
                                    <textarea
                                        id="direccion"
                                        v-model="formulario.direccion"
                                        rows="3"
                                        class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    ></textarea>
                                </label>
                            </div>
                            <footer class="mt-6 flex justify-end gap-2">
                                <button
                                    type="button"
                                    class="flex items-center justify-center gap-2 rounded bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                                    @click="cerrarModal"
                                >
                                    <SquareX /> Cancelar
                                </button>

                                <button
                                    type="submit"
                                    class="flex items-center justify-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                                >
                                    <Save /> Guardar
                                </button>
                            </footer>
                        </form>
                    </div>
                </div>

                <!-- Modal Editar Cliente -->
                <div
                    class="fixed inset-0 z-50 grid place-content-center bg-black/50 p-4"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="modalTitle"
                    v-if="mostrarModalEditar"
                >
                    <div
                        class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-gray-900"
                    >
                        <h2
                            id="modalTitle"
                            class="text-xl font-bold text-gray-900 sm:text-2xl dark:text-white"
                        >
                            Editar Cliente
                        </h2>

                        <form class="mt-4" @submit.prevent="actualizarFormulario">
                            <div class="mb-3">
                                <label for="nombre">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                    >
                                        Nombre *
                                    </span>
                                    <input
                                        type="text"
                                        id="nombre"
                                        v-model="formulario.nombre"
                                        required
                                        class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    />
                                </label>
                            </div>
                            <div class="mb-3">
                                <label for="email">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                    >
                                        Email *
                                    </span>
                                    <input
                                        type="email"
                                        id="email"
                                        v-model="formulario.email"
                                        required
                                        class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    />
                                </label>
                            </div>
                            <div class="mb-3">
                                <label for="telefono">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                    >
                                        Teléfono
                                    </span>
                                    <input
                                        type="text"
                                        id="telefono"
                                        v-model="formulario.telefono"
                                        class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    />
                                </label>
                            </div>
                            <div class="mb-3">
                                <label for="direccion">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                    >
                                        Dirección
                                    </span>
                                    <textarea
                                        id="direccion"
                                        v-model="formulario.direccion"
                                        rows="3"
                                        class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    ></textarea>
                                </label>
                            </div>
                            <footer class="mt-6 flex justify-end gap-2">
                                <button
                                    type="button"
                                    class="flex items-center justify-center gap-2 rounded bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                                    @click="cerrarModalEditar"
                                >
                                    <SquareX /> Cancelar
                                </button>

                                <button
                                    type="submit"
                                    class="flex items-center justify-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                                >
                                    <Save /> Guardar
                                </button>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-2 md:mx-10 lg:mx-20">
            <div class="mb-3 flex flex-row gap-4">
                <a
                    class="group relative inline-flex items-center overflow-hidden rounded-sm border border-current px-8 py-3 text-indigo-600 dark:text-white"
                    href="#"
                    @click="abrirModal"
                >
                    <span
                        class="absolute -start-full transition-all group-hover:start-4"
                    >
                        <Plus />
                    </span>
                    <span
                        class="text-sm font-medium transition-all group-hover:ms-4"
                    >
                        Agregar
                    </span>
                </a>

                <a
                    class="group relative inline-flex items-center overflow-hidden rounded-sm border border-current px-8 py-3 text-rose-500 dark:text-white"
                    href="#"
                    @click="exportarPdf"
                >
                    <span
                        class="absolute -start-full transition-all group-hover:start-4"
                    >
                        <FileText />
                    </span>
                    <span
                        class="text-sm font-medium transition-all group-hover:ms-4"
                    >
                        Exportar PDF
                    </span>
                </a>
            </div>

            <div
                class="bg-neutral-primary-soft rounded-base border-default relative overflow-x-auto border shadow-xs"
            >
                <table
                    class="text-body w-full text-left text-sm rtl:text-right"
                >
                    <thead
                        class="text-body bg-neutral-secondary-soft rounded-base border-default border-b text-sm"
                    >
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Nombre
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Teléfono
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Dirección
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Fecha Registro
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Opciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in clientes"
                            :key="item.id"
                            class="bg-neutral-primary border-default border-b"
                        >
                            <th
                                scope="row"
                                class="text-heading px-6 py-4 font-medium whitespace-nowrap"
                            >
                                {{ item.nombre }}
                            </th>
                            <td class="px-6 py-4">{{ item.email }}</td>
                            <td class="px-6 py-4">{{ item.telefono || 'No especificado' }}</td>
                            <td class="px-6 py-4">{{ item.direccion ? (item.direccion.length > 30 ? item.direccion.substring(0, 30) + '...' : item.direccion) : 'No especificada' }}</td>
                            <td class="px-6 py-4">{{ new Date(item.created_at).toLocaleDateString() }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-row gap-4">
                                    <a
                                        class="inline-block rounded-sm border border-current px-8 py-3 text-sm text-indigo-600 transition hover:scale-110 hover:rotate-2"
                                        href="#"
                                        @click="abrirModalEditar(item)"
                                    >
                                        <Pencil />
                                    </a>
                                    <a
                                        class="inline-block rounded-sm border border-current px-8 py-3 text-sm font-medium text-rose-600 transition hover:scale-110 hover:rotate-2"
                                        href="#"
                                        @click="confirmacion(item.id)"
                                    >
                                        <Trash2 />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>