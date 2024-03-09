export default [
  { heading: 'MÓDULOS' },
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
    title: 'Consejos Comunales', 
    icon: { icon: 'mdi-home-account' },
    to: 'dashboard-admin-community-councils', 
    action: 'ver', 
    subject: 'usuarios'
  },
  { 
    title: 'Inmigrantes', 
    icon: { icon: 'mdi-target-account' },
    to: 'dashboard-admin-inmigrants', 
    action: 'ver', 
    subject: 'usuarios'
  }
]
