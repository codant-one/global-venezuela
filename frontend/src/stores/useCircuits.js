import { defineStore } from 'pinia'
import Circuits from '@/api/circuits'

export const useCircuitsStores = defineStore('circuits', {
    state: () => ({
        circuits: {},
        loading: false,
        last_page: 1,
        circuitsTotalCount: 6
    }),
    getters:{
        getCircuits(){
            return this.circuits
        }
    },
    actions: {
        setLoading(payload){
            this.loading = payload
        },
        fetchCircuits(params) {
            this.setLoading(true)
            
            return Circuits.get(params)
                .then((response) => {
                    this.circuits = response.data.data.circuits.data
                    this.last_page = response.data.data.circuits.last_page
                    this.circuitsTotalCount = response.data.data.circuitsTotalCount
                })
                .catch(error => console.log(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        addCircuit(data) {
            this.setLoading(true)

            return Circuits.create(data)
                .then((response) => {
                    this.circuits.push(response.data.data.circuit)
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        showCircuit(id) {
            this.setLoading(true)

            return Circuits.show(id)
                .then((response) => {
                    if(response.data.success)
                        return Promise.resolve(response.data.data.circuit)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        updateCircuit(data, id) {
            this.setLoading(true)
            
            return Circuits.update(data, id)
                .then((response) => {
                    let pos = this.circuits.findIndex((item) => item.id === response.data.data.circuit.id)
                    this.circuits[pos] = response.data.data.circuit
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
         
        },
        deleteCircuit(id) {
            this.setLoading(true)

            return Circuits.delete(id)
                .then((response) => {
                    let index = this.circuits.findIndex((item) => item.id === id)
                    this.circuits.splice(index, 1)
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })  
        }
    }
})
