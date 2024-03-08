<script setup>


const isTreeDialog = ref(false)

const closeTreeDialog  = function(){
    isTreeDialog.value = false
}

</script>

<template>
    <!-- 👉 arbol de permisos rol -->
    <VDialog
        v-model="isTreeDialog"
        max-width="1200"
        >
        <!-- Dialog Activator -->
        <template #activator="{ props }">
            <VBtn
                v-if="$can('crear','roles')"
                v-bind="props"
                prepend-icon="tabler-sitemap"
                >
                Mapa de permisos
            </VBtn>
        </template>

        <!-- Dialog close btn -->
        <DialogCloseBtn @click="closeTreeDialog " />

        <!-- Dialog Content -->
        <VCard title="Mapa de Permisos">
            <VDivider class="mt-4"/>
            <VCardText>
                <ol id="permisos-lista">
                    <li class="n1">
                        <strong>Administrador</strong>&emsp;/&emsp;<code>Acceso a toda la aplicacion</code>
                    </li>
                    <li class="n1">
                        <strong>Ver Dashboard</strong>&emsp;/&emsp;<code>Permite ver el panel de control</code>
                    </li>
                    <ul class="tab">
                        <li class="n2">
                            <strong>Administración</strong>
                        </li>
                        <li class="tab n3">
                            <strong>Ver Roles</strong>&emsp;/&emsp;<code>Acceso de solo lectura al módulo de roles</code>
                        </li>
                        <ul class="tab">
                            <li class="tab n3">
                                <strong>Crear Roles</strong>&emsp;/&emsp;<code>Permite la creación de roles</code>
                            </li>
                            <li class="tab n3">
                                <strong>Editar Roles</strong>&emsp;/&emsp;<code>Permite la edición de roles</code>
                            </li>
                            <li class="tab n3">
                                <strong>Eliminar Roles</strong>&emsp;/&emsp;<code>Permite la eliminación de roles</code>
                            </li>
                        </ul>
                        <li class="tab n3">
                            <strong>Ver Usuarios</strong>&emsp;/&emsp;<code>Acceso de solo lectura al módulo de usuarios</code>
                        </li>
                        <ul class="tab">
                            <li class="tab n3">
                                <strong>Crear Usuarios</strong>&emsp;/&emsp;<code>Permite la creación de usuarios</code>
                            </li>
                            <li class="tab n3">
                                <strong>Editar Usuarios</strong>&emsp;/&emsp;<code>Permite la edición de usuarios</code>
                            </li>
                            <li class="tab n3">
                                <strong>Eliminar Usuarios</strong>&emsp;/&emsp;<code>Permite la eliminación de usuarios</code>
                            </li>
                        </ul>
                    </ul>
                </ol>
            </VCardText>
        </VCard>
    </VDialog>
</template>
