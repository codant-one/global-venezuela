export default [
    { 
      heading: 'REPORTES',
      action: 'ver' , 
      subject: 'reportes'
    },
    { 
      title: 'Por país de origen', 
      icon: { icon: 'mdi-chart-bar' },
      to: 'dashboard-reports-countries', 
      action: 'ver' , 
      subject: 'reportes'
    },
    { 
      title: 'Por ubicación', 
      icon: { icon: 'mdi-chart-scatter-plot' },
      to: 'dashboard-reports-locations',
      action: 'ver', 
      subject: 'reportes'
    },
    { 
      title: 'Por documentación', 
      icon: { icon: 'mdi-chart-bar-stacked' },
      to: 'dashboard-reports-documentations',
      action: 'ver', 
      subject: 'reportes'
    },
    { 
      title: 'Por estatus', 
      icon: { icon: 'mdi-chart-arc' },
      to: 'dashboard-reports-general',
      action: 'ver', 
      subject: 'reportes'
    }
  ]
  