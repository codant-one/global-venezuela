export default [
    { 
      heading: 'VOLUNTARIOS',
      action: 'ver' , 
      subject: 'voluntarios'
    },
    { 
      title: 'Estatal', 
      icon: { icon: 'mdi-map-marker-radius' },
      to: 'dashboard-volunteers-states', 
      action: 'ver' , 
      subject: 'voluntarios'
    },
    { 
      title: 'Municipal', 
      icon: { icon: 'mdi-city' },
      to: 'dashboard-volunteers-municipalities',
      action: 'ver', 
      subject: 'voluntarios'
    },
    { 
      title: 'Por Circuitos Comunales', 
      icon: { icon: 'mdi-crosshairs-gps' },
      to: 'dashboard-volunteers-circuits',
      action: 'ver', 
      subject: 'voluntarios'
    },
    { 
      title: 'De carácter individual', 
      icon: { icon: 'mdi-account-supervisor-circle' },
      to: 'dashboard-volunteers-independents',
      action: 'ver', 
      subject: 'voluntarios'
    }
  ]
  