<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { Calendar, FileText, Pencil, Plus, Save, SquareX, Trash2, User } from 'lucide-vue-next';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';

const proyectos = ref([]);
const clientes = ref([]);
const miNombre = ref('');
const mostrarModal = ref(false);
const mostrarModalEditar = ref(false);
const idProyecto = ref('');

//Formulario
const formulario = ref({
    nombre: '',
    descripcion: '',
    fecha_inicio: '',
    fecha_fin: '',
    estado: 'pendiente',
    cliente_id: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Proyectos',
        href: dashboard().url,
    },
];

const listarProyecto = async () => {
    try {
        const respuesta = await axios.get('/proyectos/listar');
        console.log(respuesta);
        if (respuesta.data.success) {
            proyectos.value = respuesta.data.data;
            miNombre.value = respuesta.data.nombre;
        }
    } catch (error: any) {
        console.error('Error al listar proyectos:', error);
    }
};

const listarClientes = async () => {
    try {
        const respuesta = await axios.get('/clientes/listar');
        if (respuesta.data.success) {
            clientes.value = respuesta.data.data;
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
        descripcion: '',
        fecha_inicio: '',
        fecha_fin: '',
        estado: 'pendiente',
        cliente_id: '',
    };
};

const cerrarModal = () => {
    mostrarModal.value = false;
};

//funciones para manipular el modal editar
const abrirModalEditar = (dataProyecto: any) => {
    mostrarModalEditar.value = true;
    idProyecto.value = dataProyecto.id;

    console.log('Datos proyecto:', dataProyecto);

    formulario.value.nombre = dataProyecto.nombre;
    formulario.value.descripcion = dataProyecto.descripcion || '';
    formulario.value.fecha_inicio = dataProyecto.fecha_inicio ? dataProyecto.fecha_inicio.split('T')[0] : '';
    formulario.value.fecha_fin = dataProyecto.fecha_fin ? dataProyecto.fecha_fin.split('T')[0] : '';
    formulario.value.estado = dataProyecto.estado;
    formulario.value.cliente_id = dataProyecto.cliente_id;
};

const cerrarModalEditar = () => {
    mostrarModalEditar.value = false;
};

const enviarFormulario = async () => {
    console.log('Enviando formulario proyecto');
    console.log(formulario.value);

    try {
        const respuesta = await axios.post('/proyectos/guardar', formulario.value);
        if (respuesta.data.success) {
            Swal.fire({
                title: 'Recurso Creado',
                text: 'Proyecto Creado',
                icon: 'success',
            });
            mostrarModal.value = false;
            listarProyecto();
        } else {
            Swal.fire({
                title: 'Error al crear',
                text: 'Proyecto no creado',
                icon: 'error',
            });
        }
    } catch (error: any) {
        let errorMessage = 'Error al crear proyecto';
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
    console.log('Actualizando proyecto');
    console.log(formulario.value);

    try {
        const respuesta = await axios.put(`/proyectos/editar/${idProyecto.value}`, formulario.value);
        if (respuesta.data.success) {
            Swal.fire({
                title: 'Recurso Actualizado',
                text: 'Proyecto Actualizado',
                icon: 'success',
            });
            mostrarModalEditar.value = false;
            listarProyecto();
        }
    } catch (error: any) {
        let errorMessage = 'Error al actualizar proyecto';
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
    const url = '/proyectos/exportar-pdf';
    window.location.href = url;
};

const eliminarProyecto = async (id: number) => {
    try {
        const respuesta = await axios.delete(`/proyectos/eliminar/${id}`);
        console.log(respuesta);
        if (respuesta.data.success) {
            Swal.fire({
                title: 'Recurso Eliminado',
                text: 'Proyecto Eliminado',
                icon: 'success',
            });
        }
        listarProyecto();
    } catch (error: any) {
        Swal.fire({
            title: 'Error',
            text: 'No se pudo eliminar el proyecto',
            icon: 'error',
        });
    }
};

const confirmacion = (id: number) => {
    Swal.fire({
        title: '¿Estás Seguro?',
        text: 'El proyecto se eliminará de forma permanente',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarProyecto(id);
        }
    });
};

const getEstadoTexto = (estado: string) => {
    switch (estado) {
        case 'pendiente': return 'Pendiente';
        case 'en_progreso': return 'En Progreso';
        case 'completado': return 'Completado';
        case 'cancelado': return 'Cancelado';
        default: return estado;
    }
};

const getEstadoColor = (estado: string) => {
    switch (estado) {
        case 'pendiente': return 'bg-yellow-100 text-yellow-800';
        case 'en_progreso': return 'bg-blue-100 text-blue-800';
        case 'completado': return 'bg-green-100 text-green-800';
        case 'cancelado': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const calcularDuracion = (fechaInicio: string, fechaFin: string) => {
    if (!fechaInicio || !fechaFin) return 'No definida';
    
    const inicio = new Date(fechaInicio);
    const fin = new Date(fechaFin);
    const diffTime = Math.abs(fin.getTime() - inicio.getTime());
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    return `${diffDays} días`;
};

onMounted(() => {
    listarProyecto();
    listarClientes();
});
</script>

<template>
    <Head title="Proyectos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col items-center justify-center">
            <div class="mt-4">
                <p class="text-2xl text-amber-600">Gestión de Proyectos 😊</p>
                <small>{{ miNombre }}</small>
            </div>
            <div>
                <!-- Modal registro proyecto -->
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
                            Registro de Proyecto
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
                                <label for="descripcion">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                    >
                                        Descripción
                                    </span>
                                    <textarea
                                        id="descripcion"
                                        v-model="formulario.descripcion"
                                        rows="3"
                                        class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    ></textarea>
                                </label>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label for="fecha_inicio">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                            Fecha Inicio
                                        </span>
                                        <input
                                            type="date"
                                            id="fecha_inicio"
                                            v-model="formulario.fecha_inicio"
                                            class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                        />
                                    </label>
                                </div>
                                <div>
                                    <label for="fecha_fin">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                            Fecha Fin
                                        </span>
                                        <input
                                            type="date"
                                            id="fecha_fin"
                                            v-model="formulario.fecha_fin"
                                            :min="formulario.fecha_inicio"
                                            class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                        />
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="estado">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Estado *
                                    </span>
                                    <select
                                        id="estado"
                                        v-model="formulario.estado"
                                        required
                                        class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    >
                                        <option value="pendiente">Pendiente</option>
                                        <option value="en_progreso">En Progreso</option>
                                        <option value="completado">Completado</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </label>
                            </div>
                            <div class="mb-3">
                                <label for="cliente_id">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Cliente *
                                    </span>
                                    <select
                                        id="cliente_id"
                                        v-model="formulario.cliente_id"
                                        required
                                        class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    >
                                        <option value="">Seleccione un cliente</option>
                                        <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                            {{ cliente.nombre }} ({{ cliente.email }})
                                        </option>
                                    </select>
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

                <!-- Modal Editar Proyecto -->
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
                            Editar Proyecto
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
                                <label for="descripcion">
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                    >
                                        Descripción
                                    </span>
                                    <textarea
                                        id="descripcion"
                                        v-model="formulario.descripcion"
                                        rows="3"
                                        class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    ></textarea>
                                </label>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label for="fecha_inicio">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                            Fecha Inicio
                                        </span>
                                        <input
                                            type="date"
                                            id="fecha_inicio"
                                            v-model="formulario.fecha_inicio"
                                            class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                        />
                                    </label>
                                </div>
                                <div>
                                    <label for="fecha_fin">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                            Fecha Fin
                                        </span>
                                        <input
                                            type="date"
                                            id="fecha_fin"
                                            v-model="formulario.fecha_fin"
                                            :min="formulario.fecha_inicio"
                                            class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                        />
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="estado">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Estado *
                                    </span>
                                    <select
                                        id="estado"
                                        v-model="formulario.estado"
                                        required
                                        class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    >
                                        <option value="pendiente">Pendiente</option>
                                        <option value="en_progreso">En Progreso</option>
                                        <option value="completado">Completado</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </label>
                            </div>
                            <div class="mb-3">
                                <label for="cliente_id">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Cliente *
                                    </span>
                                    <select
                                        id="cliente_id"
                                        v-model="formulario.cliente_id"
                                        required
                                        class="mt-0.5 w-full rounded border-gray-300 p-2 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    >
                                        <option value="">Seleccione un cliente</option>
                                        <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                            {{ cliente.nombre }} ({{ cliente.email }})
                                        </option>
                                    </select>
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
                                Descripción
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Cliente
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Fechas
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Duración
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Estado
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Opciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in proyectos"
                            :key="item.id"
                            class="bg-neutral-primary border-default border-b"
                        >
                            <th
                                scope="row"
                                class="text-heading px-6 py-4 font-medium whitespace-nowrap"
                            >
                                {{ item.nombre }}
                            </th>
                            <td class="px-6 py-4">
                                {{ item.descripcion ? (item.descripcion.length > 30 ? item.descripcion.substring(0, 30) + '...' : item.descripcion) : 'Sin descripción' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <User class="h-4 w-4" />
                                    {{ item.cliente?.nombre || 'No asignado' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="flex items-center gap-1">
                                        <Calendar class="h-3 w-3" />
                                        Inicio: {{ item.fecha_inicio ? new Date(item.fecha_inicio).toLocaleDateString() : 'No definida' }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <Calendar class="h-3 w-3" />
                                        Fin: {{ item.fecha_fin ? new Date(item.fecha_fin).toLocaleDateString() : 'No definida' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                {{ calcularDuracion(item.fecha_inicio, item.fecha_fin) }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="['px-2 py-1 rounded-full text-xs font-medium', getEstadoColor(item.estado)]">
                                    {{ getEstadoTexto(item.estado) }}
                                </span>
                            </td>
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