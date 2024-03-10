<script setup>

import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { requiredValidator } from '@validators'
import { avatarText } from '@/@core/utils/formatters'
import { useCommunityCouncilsStores } from '@/stores/useCommunityCouncils'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  communityCouncil: {
    type: Object,
    required: false,
  },
  states: {
    type: Object,
    required: true
  },
  municipalities: {
    type: Object,
    required: true
  },
  parishes: {
    type: Object,
    required: true
  },
  circuits: {
    type: Object,
    required: true
  }
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'communityCouncilData',
])

const communityCouncilsStores = useCommunityCouncilsStores()

const isFormValid = ref(false)
const refForm = ref()

const listStates = ref(props.states)
const listMunicipalities = ref(props.municipalities)
const listParishes = ref(props.parishes)
const listCircuits = ref(props.circuits)

const listMunicipalitiesByStates = ref([])
const listParishesByMunicipalities = ref([])
const listCircuitsByParishes = ref([])

const id = ref(0)
const name = ref('')
const state_id = ref('')
const stateOld_id = ref('')
const municipality_id = ref('')
const municipalityOld_id = ref('')
const parish_id = ref('')
const parishOld_id = ref('')
const circuit_id = ref('')
const circuitOld_id = ref('')
const isEdit = ref(false)

const getTitle = computed(() => {
  return isEdit.value ? 'Actualizar Consejo Comunal': 'Agregar Consejo Comunal'
})

watchEffect(async() => {
  if (props.isDrawerOpen) {
    if (!(Object.entries(props.communityCouncil).length === 0) && props.communityCouncil.constructor === Object) {
      isEdit.value = true
      id.value = props.communityCouncil.id

      stateOld_id.value = props.communityCouncil.circuit.parish.municipality.state.id
      state_id.value = props.communityCouncil.circuit.parish.municipality.state.name

      municipalityOld_id.value = props.communityCouncil.circuit.parish.municipality.id
      municipality_id.value = props.communityCouncil.circuit.parish.municipality.name

      parishOld_id.value = props.communityCouncil.circuit.parish.id
      parish_id.value = props.communityCouncil.circuit.parish.name

      circuitOld_id.value = props.communityCouncil.circuit.id
      circuit_id.value = props.communityCouncil.circuit.name

      name.value = props.communityCouncil.name
    }
  }
})

const getMunicipalities = computed(() => {
  return listMunicipalitiesByStates.value.map((state) => {
    return {
      title: state.name,
      value: state.id,
    }
  })
})

const getParishes = computed(() => {
  return listParishesByMunicipalities.value.map((municipality) => {
    return {
      title: municipality.name,
      value: municipality.id,
    }
  })
})

const getCircuits = computed(() => {
  return listCircuitsByParishes.value.map((parish) => {
    return {
      title: parish.name,
      value: parish.id,
    }
  })
})

const selectState = state => {
  if (state) {
    let _state = listStates.value.find(item => item.name === state)
    state_id.value = _state.name
 
    municipality_id.value = ''

    listMunicipalitiesByStates.value = listMunicipalities.value.filter(item => item.state_id === _state.id)
  }
}

const selectMunicipalities = municipality => {
  if (municipality) {
    let _municipality = listMunicipalities.value.find(item => item.id === municipality)
    municipality_id.value = _municipality.name
 
    parish_id.value = ''

    listParishesByMunicipalities.value = listParishes.value.filter(item => item.municipality_id === _municipality.id)

  }
}

const selectParishes = parish => {
  if (parish) {
    let _parish = listParishes.value.find(item => item.id === parish)
    parish_id.value = _parish.name
 
    circuit_id.value = ''

    listCircuitsByParishes.value = listCircuits.value.filter(item => item.parish_id === _parish.id)

  }
}

// 👉 drawer close
const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()

    name.value = null
    isEdit.value = false
    id.value = 0
  })
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {

      let data = {
        name: name.value,
        circuit_id: (Number.isInteger(circuit_id.value)) ? circuit_id.value : circuitOld_id.value,
      }

      closeNavigationDrawer()

      emit('communityCouncilData', { data: data, id: id.value }, isEdit.value ? 'update' : 'create')
      emit('update:isDrawerOpen', false)
      
    }
  })
}

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}
</script>

<template>
  <VNavigationDrawer
    temporary
    :width="400"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Title -->
    <div class="d-flex align-center pa-6 pb-1">
      <h6 class="text-h6">
        {{ getTitle }}
      </h6>

      <VSpacer />

      <!-- 👉 Close btn -->
      <VBtn
        variant="tonal"
        color="default"
        icon
        size="32"
        class="rounded"
        @click="closeNavigationDrawer"
      >
        <VIcon
          size="18"
          icon="tabler-x"
        />
      </VBTn>
    </div>

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <!-- 👉 Form -->
          <VForm
            ref="refForm"
            v-model="isFormValid"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 State -->
              <VCol cols="12">
                <VAutocomplete
                  v-model="state_id"
                  label="Estado"
                  :rules="[requiredValidator]"
                  :items="listStates"
                  item-title="name"
                  item-value="name"
                  :menu-props="{ maxHeight: '200px' }"
                  @update:model-value="selectState"
                />
              </VCol>

              <!-- 👉 Municipality -->
              <VCol cols="12">
                <VAutocomplete
                  v-model="municipality_id"
                  label="Municipio"
                  :rules="[requiredValidator]"
                  :items="getMunicipalities"
                  :menu-props="{ maxHeight: '200px' }"
                  @update:model-value="selectMunicipalities"
                />
              </VCol>

              <!-- 👉 Parish -->
              <VCol cols="12">
                <VAutocomplete
                  v-model="parish_id"
                  label="Parroquia"
                  :rules="[requiredValidator]"
                  :items="getParishes"
                  :menu-props="{ maxHeight: '200px' }"
                  @update:model-value="selectParishes"
                />
              </VCol>

              <!-- 👉 Circuit -->
              <VCol cols="12">
                <VAutocomplete
                  v-model="circuit_id"
                  label="Circuito"
                  :rules="[requiredValidator]"
                  :items="getCircuits"
                  :menu-props="{ maxHeight: '200px' }"
                />
              </VCol>

              <!-- 👉 Name -->
              <VCol cols="12">
                <VTextField
                  v-model="name"
                  :rules="[requiredValidator]"
                  label="Nombre del consejo comunal"
                />
              </VCol>

              <!-- 👉 aa -->
              <VCol cols="12">
               
              </VCol>

              <!-- 👉 Submit and Cancel -->
              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-3"
                >
                  {{ isEdit ? 'Actualizar': 'Agregar' }}
                </VBtn>
                <VBtn
                  type="reset"
                  variant="tonal"
                  color="secondary"
                  @click="closeNavigationDrawer"
                >
                  Cancelar
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>
