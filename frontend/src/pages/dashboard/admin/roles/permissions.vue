<script setup>

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true
  },
  role: {
    type: Object,
    required: false
  }
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'permissions'
])

const assignedPermissions =  ref([])

watchEffect(() => {
    if (props.isDrawerOpen && props.role) {

        if (!(Object.entries(props.role).length === 0) && props.role.constructor === Object) {
            assignedPermissions.value = props.role.assignedPermissions
        }
    }
})

const closeModal = function(){
    emit('update:isDrawerOpen', false)
    emit('permissions', assignedPermissions.value)
}

</script>

<template>
    <!-- DIALOGO DE ROLES CREAR -->
    <VDialog
        :model-value="props.isDrawerOpen"
        persistent
        max-width="1100"
        >
        <DialogCloseBtn @click="closeModal" />

        <VCard title="Permisos">
            <VDivider class="mt-4"/>
            <VCardText>
                <VCardTitle>
                    Administrador General  
                </VCardTitle>
                <VCardText>
                    <div class="ml-5">
                        <VLabel style="font-weight: bold;">
                            Administrador
                        </VLabel>
                        <div class="demo-space-x ml-5">
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="administrador"
                                value="administrador"
                            />
                        </div>
                    </div>
                </VCardText>
                <VCardTitle>
                    General  
                </VCardTitle>
                <VCardText>
                    <div class="ml-5">
                        <VLabel style="font-weight: bold;">
                            Panel de Control
                        </VLabel>
                        <div class="demo-space-x ml-5">
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="ver dashboard"
                                value="ver dashboard"
                            />
                        </div>
                    </div>
                </VCardText>
                <VCardTitle>
                    Administracion  
                </VCardTitle>
                <VCardText>
                    <div class="ml-5">
                        <VLabel style="font-weight: bold;">
                            Roles
                        </VLabel>
                        <div class="demo-space-x ml-5">
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="ver roles"
                                value="ver roles"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="crear roles"
                                value="crear roles"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="editar roles"
                                value="editar roles"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="eliminar roles"
                                value="eliminar roles"
                            />
                        </div>
                        <VLabel style="font-weight: bold;">
                            Usuarios
                        </VLabel>
                        <div class="demo-space-x ml-5">
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="ver usuarios"
                                value="ver usuarios"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="crear usuarios"
                                value="crear usuarios"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="editar usuarios"
                                value="editar usuarios"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="eliminar usuarios"
                                value="eliminar usuarios"
                            />
                        </div>
                        <VLabel style="font-weight: bold;">
                            Circuitos
                        </VLabel>
                        <div class="demo-space-x ml-5">
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="ver circuitos"
                                value="ver circuitos"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="crear circuitos"
                                value="crear circuitos"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="editar circuitos"
                                value="editar circuitos"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="eliminar circuitos"
                                value="eliminar circuitos"
                            />
                        </div>
                        <VLabel style="font-weight: bold;">
                            Consejos Comunales
                        </VLabel>
                        <div class="demo-space-x ml-5">
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="ver consejos-comunales"
                                value="ver consejos-comunales"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="crear consejos-comunales"
                                value="crear consejos-comunales"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="editar consejos-comunales"
                                value="editar consejos-comunales"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="eliminar consejos-comunales"
                                value="eliminar consejos-comunales"
                            />
                        </div>
                        <VLabel style="font-weight: bold;">
                            Inmigrantes
                        </VLabel>
                        <div class="demo-space-x ml-5">
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="ver inmigrantes"
                                value="ver inmigrantes"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="crear inmigrantes"
                                value="crear inmigrantes"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="editar inmigrantes"
                                value="editar inmigrantes"
                            />
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="eliminar inmigrantes"
                                value="eliminar inmigrantes"
                            />
                        </div>
                    </div>
                </VCardText>
                <VCardTitle>
                    Reportes  
                </VCardTitle>
                <VCardText>
                    <div class="ml-5">
                        <div class="demo-space-x ml-5">
                            <VCheckbox
                                v-model="assignedPermissions"
                                label="ver reportes"
                                value="ver reportes"
                            />
                        </div>
                    </div>
                </VCardText>
            </VCardText>
            <VCardText class="d-flex flex-wrap gap-3">
                <VSpacer />
                <VBtn @click="closeModal">
                    Aceptar
                </VBtn>
            </VCardText>
        </VCard>
    </VDialog>
</template>
