var calendarEl = document.getElementById('calendar');

var calendar = new FullCalendar.Calendar(calendarEl, {
  themeSystem: 'bootstrap5',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'multiMonthYear,dayGridMonth,timeGridWeek'
  },
  initialView: 'multiMonthYear',
  initialDate: '2023-11-12',
  editable: false,
  selectable: true,
  dayMaxEvents: true,
  events: '/plannerfocus/MVC/Controller/taskController.class.php?command=getAllCalendar',
  locale: 'pt-br',
  buttonText: {
    today: 'Hoje',
    year: 'Ano',
    month: 'Mês',
    week: 'Semana'
  },
  eventClick: function(info) {
    var title = info.event.title;
    var start = info.event.start;
    var description = info.event.extendedProps.description;

    // Abre um modal Bootstrap para exibir as informações do evento
    $('#eventModal').modal('show');
    $('#eventModalTitle').text(title);

    if (info.event.allDay) {
      $('#eventModalStart').text('Início: ' + start.toLocaleDateString());
    } else {
      $('#eventModalStart').text('Início: ' + start.toLocaleString());
    }
    if (description.trim() !== '') { // Verifica se a descrição não está vazia
      $('#eventModalDescription').text('Descrição: ' + description);
    }else{
      $('#eventModalDescription').text('');
    }
  }
});

calendar.on('dateClick', function(info) {
  console.log(info);
});

calendar.render();