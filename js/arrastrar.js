let draggedItem = null;
let marker = null;  // Elemento ficticio para marcar la posición de inserción

// Evento cuando empieza el arrastre
function dragStart(e) {
  draggedItem = e.target;
  draggedItem.classList.add('dragging');
}

// Evento cuando el ratón entra en un 'li' destino
function dragEnter(e) {
  e.preventDefault();  // Evitar comportamiento predeterminado (por ejemplo, abrir el archivo)
  const target = e.target;

  // Aseguramos que estamos trabajando con un 'li' o un elemento adecuado
  if (target.classList.contains('item') && target !== draggedItem) {
    const bounding = target.getBoundingClientRect();
    const offset = e.clientY - bounding.top;  // Posición del ratón respecto al 'li'
    const height = bounding.height;

    // Eliminamos el marcador previo si existe
    if (marker) {
      marker.remove();
    }

    target.classList.add('li-border');

    /*
    // Si el ratón está en la parte superior del 'li', se inserta antes
    if (offset < (height / 3) + 2) {
        console.log('antes');
      marker = document.createElement('div');
      marker.classList.add('marker-before');
      target.parentNode.insertBefore(marker, target);
    }
    // Si está en la parte inferior, se inserta después
    else if (offset > (height * 2 / 3) - 2) {
        console.log('despues');
      marker = document.createElement('div');
      marker.classList.add('marker-after');
      target.parentNode.insertBefore(marker, target.nextSibling);
    }
    // Si está en el centro, se inserta dentro
    else {
      //marker = document.createElement('div');
      //marker.classList.add('marker-inside');
      //target.appendChild(marker);
      target.style.backgroundColor='lightblue';
    }*/
  }
}

// Evento cuando el ratón se mueve sobre el área (permite que el drop ocurra)
function dragOver(e) {
  e.preventDefault();  // Habilitar el drop
}

// Evento cuando se suelta el elemento arrastrado
function drop(e) {
  e.preventDefault();

  // Si existe el marcador, colocamos el item en la posición indicada
  if (marker) {
    if (marker.classList.contains('marker-before')) {
      marker.parentNode.insertBefore(draggedItem, marker);
    } else if (marker.classList.contains('marker-after')) {
      marker.parentNode.insertBefore(draggedItem, marker.nextSibling);
    } else if (marker.classList.contains('marker-inside')) {
      const nestedList = marker.querySelector('ul') || document.createElement('ul');
      marker.appendChild(nestedList);
      nestedList.appendChild(draggedItem);
    }

    // Eliminar el marcador después de soltar el item
    marker.remove();
  }

  // Limpiar la clase de arrastre
  draggedItem.classList.remove('dragging');
  marker = null;  // Restablecer marcador
}

// Evento cuando el ratón sale de la lista o cuando se suelta fuera de la lista
function dragLeave(e) {
    e.target.classList.remove('li-border');
  // Si el ratón sale del área de la lista, eliminamos el marcador
  if (marker) {
    marker.remove();
    marker = null;
  }
}

// Agregar eventos a los elementos
document.querySelectorAll('.item').forEach(item => {
  item.addEventListener('dragstart', dragStart);
  item.addEventListener('dragenter', dragEnter);
  item.addEventListener('dragover', dragOver);
  item.addEventListener('drop', drop);
  item.addEventListener('dragleave', dragLeave);
});

// También permitir que el contenedor de la lista acepte el drop
const dropList = document.querySelector('.drop-list');
dropList.addEventListener('dragover', dragOver);
dropList.addEventListener('drop', drop);
dropList.addEventListener('dragleave', dragLeave);  // Eliminar marcador si el ratón sale del contenedor


/*
  // Verificar que el destino es una lista válida
  if (target.classList.contains('drop-list') || target.classList.contains('item')) {
    // Si el destino es una lista, insertamos el elemento arrastrado en ella
    target.appendChild(draggedItem);
  }

  */