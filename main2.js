let nav = 0;
let clicked = null;
let events = [];
const roomColors = {};

const calendar = document.getElementById('calendar');
const weekdays = ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'];

function load() {
  const date = new Date();
  date.setMonth(date.getMonth() + nav); // Atualiza a data com base no nav

  const day = date.getDate();
  const month = date.getMonth();
  const year = date.getFullYear();

  const daysMonth = new Date(year, month + 1, 0).getDate();
  const firstDayMonth = new Date(year, month, 1);

  const dateString = firstDayMonth.toLocaleDateString('pt-br', {
    weekday: 'long',
    year: 'numeric',
    month: 'numeric',
    day: 'numeric',
  });

  const paddingDays = weekdays.indexOf(dateString.split(', ')[0]);

  document.getElementById('monthDisplay').innerText = `${date.toLocaleDateString('pt-br', { month: 'long' })}, ${year}`;

  calendar.innerHTML = '';

  for (let i = 1; i <= paddingDays + daysMonth; i++) {
    const dayS = document.createElement('div');
    dayS.classList.add('day');

    if (i > paddingDays) {
      const dayNumber = i - paddingDays;
      const dayString = `${year}-${String(month + 1).padStart(2, '0')}-${String(dayNumber).padStart(2, '0')}`;

      if (dayNumber >= day && nav === 0) {
        dayS.addEventListener('click', () => {
          window.location.href = `/lista_reservas_dia.php?dia=${dayNumber}&mes=${month + 1}&ano=${year}`;
        });
      }
      if (nav > 0) {
        dayS.addEventListener('click', () => {
          window.location.href = `/lista_reservas_dia.php?dia=${dayNumber}&mes=${month + 1}&ano=${year}`;
        });
      }

      const dayLink = document.createElement('span');
      dayLink.innerText = dayNumber;
      dayS.appendChild(dayLink);

      if (dayNumber === day && nav === 0) {
        dayS.id = 'currentDay';
      }

      const eventDay = events.filter(event => event.date.startsWith(dayString));

      if (eventDay.length > 0) {
        eventDay.forEach(event => {
          const eventP = document.createElement('p');
          eventP.classList.add('event');
          eventP.classList.add(roomColors[event.room]); // Apply unique class for each room
          eventP.innerText = event.title;
          dayS.appendChild(eventP);
        });
      }
    } else {
      dayS.classList.add('padding');
      dayS.classList.add('A_padding');
    }

    calendar.appendChild(dayS);
  }
}

function buttons() {
  document.getElementById('backButton').addEventListener('click', () => {
    nav--;
    const date = new Date();
    date.setMonth(date.getMonth() + nav);
    load();
    fetchEvents(date.getFullYear(), date.getMonth() + 1);
  });

  document.getElementById('nextButton').addEventListener('click', () => {
    nav++;
    const date = new Date();
    date.setMonth(date.getMonth() + nav);
    load();
    fetchEvents(date.getFullYear(), date.getMonth() + 1);
  });

  document.getElementById('minhas_salas').addEventListener('click', () => {
    const date = new Date();
    date.setMonth(date.getMonth() + nav);
    const year = date.getFullYear();
    const month = date.getMonth() + 1;
    fetchEvents2(year, month);
  });
}

function fetchEvents(year, month) {
  $.ajax({
    type: "GET",
    url: "get_reserva.php",
    data: {
      ano: year,
      mes: month
    },
    success: function (response) {
      try {
        const reservas = response;
        let colorIndex = 0;
        const colors = ['color1', 'color2', 'color3', 'color4', 'color5']; // Define your colors here

        events = reservas.map(reserva => {
          if (!roomColors[reserva.nome_sala]) {
            roomColors[reserva.nome_sala] = colors[colorIndex % colors.length];
            colorIndex++;
          }
          return {
            date: reserva.data_inicio.split(' ')[0],
            title: ` ${reserva.nome_sala} 
            ${reserva.data_inicio.slice(-8, -3)} - ${reserva.data_fim.slice(-8, -3)}`,
            room: reserva.nome_sala
          };
        });
        load();
      } catch (e) {
        console.error("Failed to process response:", e);
      }
    }
  });
}

function fetchEvents2(year, month) {
  $.ajax({
    type: "GET",
    url: "get_minhas_reservas.php",
    data: {
      ano: year,
      mes: month
    },
    success: function (response) {
      try {
        const reservas = response;
        let colorIndex = 0;
        const colors = ['color1', 'color2', 'color3', 'color4', 'color5']; // Define your colors here

        events = reservas.map(reserva => {
          if (!roomColors[reserva.nome_sala]) {
            roomColors[reserva.nome_sala] = colors[colorIndex % colors.length];
            colorIndex++;
          }
          return {
            date: reserva.data_inicio.split(' ')[0],
            title: ` ${reserva.nome_sala}
             ${reserva.data_inicio.slice(-8, -3)} - ${reserva.data_fim.slice(-8, -3)}`,
            room: reserva.nome_sala
          };
        });

        load();
      } catch (e) {
        console.error("Failed to process response:", e);
      }
    }
  });
}

buttons();
load();
const currentDate = new Date();
fetchEvents(currentDate.getFullYear(), currentDate.getMonth() + 1);