export default [
  { 
    heading: 'MÓDULOS',
    action: 'ver' , 
    subject: 'migrantes' 
  },
  { 
    title: 'Roles', 
    icon: { icon: 'mdi-account-lock-open' },
    to: 'dashboard-admin-roles', 
    action: 'ver' , 
    subject: 'roles'
  },
  { 
    title: 'Usuarios', 
    icon: { icon: 'mdi-account' },
    to: 'dashboard-admin-users', 
    action: 'ver', 
    subject: 'usuarios'
  },
  { 
    title: 'Circuitos', 
    icon: { icon: 'mdi-home-group' },
    to: 'dashboard-admin-circuits', 
    action: 'ver', 
    subject: 'circuitos'
  },
  { 
    title: 'Consejos Comunales', 
    icon: { icon: 'mdi-home-account' },
    to: 'dashboard-admin-community-councils', 
    action: 'ver', 
    subject: 'consejos-comunales'
  },
  { 
    title: 'Migrantes', 
    icon: { icon: 'mdi-target-account' },
    to: 'dashboard-admin-migrants', 
    action: 'ver', 
    subject: 'migrantes'
  },
  { 
    title: 'Agregar Migrante', 
    icon: { icon: 'mdi-target-account' },
    to: 'dashboard-admin-migrants-add', 
    action: 'crear', 
    subject: 'operador-migrantes'
  }
]
