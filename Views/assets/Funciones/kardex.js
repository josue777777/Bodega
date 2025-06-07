const dataKardex = [
  {
    fecha: "22/04/2025",
    entidad: "ALMACEN GENERAL ",
    factura: "292384576",
    entra: "1.00",
    sale: "0.00",
    saldo: "18.00",
    lote: "h1j234",
  },
  {
    fecha: "28/10/2024",
    entidad: "714-LABORATORIO",
    factura: "292384576",
    entra: "0.00",
    sale: "2.00",
    saldo: "17.00",
    lote: "h1j234",
  },
  {
    fecha: "11/10/2024",
    entidad: "ALMACEN GENERAL ",
    factura: "292384576",
    entra: "1.00",
    sale: "0.00",
    saldo: "19.00",
    lote: "h1j234",
  },
  {
    fecha: "12/08/2024",
    entidad: "505-ODONTOLOGIA",
    factura: "292384576",
    entra: "0.00",
    sale: "1.00",
    saldo: "18.00",
    lote: "2k123",
  },
  {
    fecha: "12/08/2024",
    entidad: "811 BODEGA ENFERMERIA",
    factura: "292384576",
    entra: "0.00",
    sale: "1.00",
    saldo: "19.00",
    lote: "h1j234",
  },
  {
    fecha: "12/08/2024",
    entidad: "840-RECURSOS HUMANOS",
    factura: "292384576",
    entra: "0.00",
    sale: "2.00",
    saldo: "17.00",
    lote: "h1j234",
  },
  {
    fecha: "11/08/2024",
    entidad: "840-RECURSOS HUMANOS",
    factura: "292384576",
    entra: "0.00",
    sale: "1.00",
    saldo: "25.00",
    lote: "2k123",
    lote: "h1j234",
  },
  {
    fecha: "10/08/2024",
    entidad: "COD PROV-MATERIALES FARMANOVA",
    factura: "292384576",
    entra: "24.00",
    sale: "0.00",
    saldo: "26.00",
    lote: "h1j234",
  },
];

function loadTable(data) {
  const tbody = document.getElementById("kardexBody");
  tbody.innerHTML = "";
  data.forEach((row) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `<td>${row.fecha}</td><td>${row.entidad}</td><td>${row.factura}</td><td>${row.entra}</td><td>${row.sale}</td><td>${row.saldo}</td><td>${row.lote}</td>`;
    tbody.appendChild(tr);
  });
}

function filterTable() {
  const searchValue = document
    .getElementById("productSearch")
    .value.toLowerCase();
  const filtered = dataKardex.filter(
    (row) =>
      row.entidad.toLowerCase().includes(searchValue) ||
      row.factura.toLowerCase().includes(searchValue)
  );
  loadTable(filtered);
}

function masKardex() {
  alert("Funcionalidad para cargar más registros en futuro con base de datos.");
}

function menosKardex() {
  alert("Funcionalidad para reducir registros visibles.");
}

// Inicializa tabla
loadTable(dataKardex);
