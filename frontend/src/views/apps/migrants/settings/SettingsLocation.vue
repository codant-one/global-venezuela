<script setup>

import { ref } from 'vue'
import { requiredValidator } from '@validators'

const props = defineProps({
  migrant: {
    type: Object,
    required: false
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
  },
  communityCouncils: {
    type: Object,
    required: true
  }
})

const emit = defineEmits([
  'submit',
  'back'
])

const listStates = ref(props.states)
const listMunicipalities = ref(props.municipalities)
const listParishes = ref(props.parishes)
const listCircuits = ref(props.circuits)
const listCommunityCouncils = ref(props.communityCouncils)

const listMunicipalitiesByStates = ref([])
const listParishesByMunicipalities = ref([])
const listCircuitsByMunicipalities = ref([])
const listCommunityCouncilsByCircuits = ref([])

const refVForm = ref()

const state_id = ref('')
const stateOld_id = ref('')
const municipality_id = ref('')
const municipalityOld_id = ref('')
const parish_id = ref('')
const parishOld_id = ref('')
const circuit_id = ref('')
const circuitOld_id = ref('')
const community_council_id = ref('')

const locationDetail = ref({
  parish_id: '',
  community_council_id: null,
  address: ''
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
  return listCircuitsByMunicipalities.value.map((municipality) => {
    return {
      title: municipality.name,
      value: municipality.id,
    }
  })
})

const getCommunityCouncils = computed(() => {
  return listCommunityCouncilsByCircuits.value.map((circuit) => {
    return {
      title: circuit.name,
      value: circuit.id,
    }
  })
})

const clearState = () => {
  state_id.value = ''
  municipality_id.value = ''
  parish_id.value = ''
  locationDetail.value.parish_id = ''
  circuit_id.value = ''
  locationDetail.value.community_council_id = ''

  listMunicipalitiesByStates.value = []
  listParishesByMunicipalities.value = []
  listCircuitsByMunicipalities.value = []
  listCommunityCouncilsByCircuits.value = []
}

const clearMunicipality = () => {
  municipality_id.value = ''
  parish_id.value = ''
  locationDetail.value.parish_id = ''
  circuit_id.value = ''
  locationDetail.value.community_council_id = ''

  listParishesByMunicipalities.value = []
  listCircuitsByMunicipalities.value = []
  listCommunityCouncilsByCircuits.value = []
}

const clearParish = () => {
  parish_id.value = ''
  locationDetail.value.parish_id = ''
}

const clearCircuit = () => {
  locationDetail.value.community_council_id = ''

  listCommunityCouncilsByCircuits.value = []
}

const clearCommunityCouncil = () => {
  locationDetail.value.community_council_id = ''
}

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
 
    locationDetail.value.parish_id = ''
    parish_id.value = ''
    circuit_id.value = ''

    listParishesByMunicipalities.value = listParishes.value.filter(item => item.municipality_id === _municipality.id)
    listCircuitsByMunicipalities.value = listCircuits.value.filter(item => item.municipality_id === _municipality.id)
  }
}

const selectParishes = parish => {
  if (parish) {
    let _parish = listParishes.value.find(item => item.id === parish)
    parish_id.value = _parish.name
    locationDetail.value.parish_id = _parish.id

  }
}

const selectCircuit = circuit => {
  if (circuit) {
    let _circuit = listCircuits.value.find(item => item.id === circuit)
    circuit_id.value = _circuit.name
 
    locationDetail.value.community_council_id = ''
    listCommunityCouncilsByCircuits.value = listCommunityCouncils.value.filter(item => item.circuit_id === _circuit.id)

  }
}

watchEffect(fetchData)

async function fetchData() {

  if(props.migrant) {

    selectState(props.migrant.parish.municipality.state.name)
    selectMunicipalities(props.migrant.parish.municipality.id)
    selectParishes(props.migrant.parish.id)
    selectCircuit(props.migrant.community_council?.circuit.id)

    locationDetail.value.parish_id = props.migrant.parish_id
    locationDetail.value.community_council_id = props.migrant.community_council?.name
    locationDetail.value.address = props.migrant.address

    stateOld_id.value = props.migrant.parish.municipality.state.id
    state_id.value = props.migrant.parish.municipality.state.name

    municipalityOld_id.value = props.migrant.parish.municipality.id
    municipality_id.value = props.migrant.parish.municipality.name

    parishOld_id.value = props.migrant.parish.id
    parish_id.value = props.migrant.parish.name

    circuitOld_id.value = props.migrant.community_council?.circuit.id
    circuit_id.value = props.migrant.community_council?.circuit.name

    community_council_id.value = props.migrant.community_council_id
  }
}

const onSubmit = () => {
  refVForm.value?.validate().then(({ valid: isValid }) => {
    if (isValid) {

      locationDetail.value.community_council_id = Number(locationDetail.value.community_council_id) ? locationDetail.value.community_council_id : community_council_id.value
      emit('submit', locationDetail.value)
    }
  })
}

</script>

<template>
  <VForm
    ref="refVForm"
    @submit.prevent="onSubmit"
    >
    <VCard
      title="Ubicación"
      class="mb-6"
    >
      <VCardText>
        <VRow>
          <VCol cols="12" md="6">
            <VAutocomplete
              v-model="state_id"
              label="Estado"
              :rules="[requiredValidator]"
              :items="listStates"
              item-title="name"
              item-value="name"
              :menu-props="{ maxHeight: '200px' }"
              @update:model-value="selectState"
              @click:clear="clearState"
              clearable
            />
          </VCol>
          <VCol cols="12" md="6">
            <VAutocomplete
              v-model="municipality_id"
              label="Municipio"
              :rules="[requiredValidator]"
              :items="getMunicipalities"
              :menu-props="{ maxHeight: '200px' }"
              @update:model-value="selectMunicipalities"
              @click:clear="clearMunicipality"
              clearable
            />
          </VCol>
          <VCol cols="12" md="6">
            <VAutocomplete
              v-model="parish_id"
              label="Parroquia"
              :rules="[requiredValidator]"
              :items="getParishes"
              :menu-props="{ maxHeight: '200px' }"
              @update:model-value="selectParishes"
              @click:clear="clearParish"
              clearable
            />
          </VCol>
          <VCol cols="12" md="6">
            <VAutocomplete
              v-model="circuit_id"
              label="Circuito"
              :items="getCircuits"
              :menu-props="{ maxHeight: '200px' }"
              @update:model-value="selectCircuit"
              @click:clear="clearCircuit"
              clearable
            />
          </VCol>
          <VCol cols="12" md="6">
            <VAutocomplete
              v-model="locationDetail.community_council_id"
              label="Consejo Comunal"
              :items="getCommunityCouncils"
              :menu-props="{ maxHeight: '200px' }"
              @click:clear="clearCommunityCouncil"
              clearable
            />
          </VCol>
          <VCol cols="12" md="12">
            <VTextarea
              v-model="locationDetail.address"
              rows="3"
              label="Dirección"
              :rules="[requiredValidator]"
            />
          </VCol>
        </VRow>
      </VCardText>
    </VCard>

    <div class="d-flex justify-end gap-x-4 mt-6">
      <VBtn
        color="secondary"
        variant="tonal"
        @click="emit('back')"
      >
        Atrás
      </VBtn>
      <VBtn type="submit">Siguiente</VBtn>
    </div>
  </VForm>
</template>
