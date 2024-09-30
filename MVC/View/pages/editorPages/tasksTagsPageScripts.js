// Aguarde o documento HTML estar totalmente carregado
$(document).ready(function(){
  loadTags();
  insertTask();

// Adicione um evento de change ao selectTag
document.getElementById('selectTag').addEventListener('change', function() {
  // Obtém o valor selecionado no select
  const selectedTagId = this.value;
  // Verifica se a opção selecionada não é a padrão
  if (selectedTagId !== '') {
    // Faça uma solicitação AJAX para obter as tarefas com a etiqueta selecionada
    $.post('/plannerfocus/MVC/Controller/taskTagController.class.php?command=getAllbyTag', { id_tag: selectedTagId }, function (data) {
      const taskTagData = JSON.parse(data);

      // Limpe a lista de tarefas existente
      const taskList = document.getElementById('list');
      taskList.innerHTML = '';

      // Exiba as tarefas relacionadas à etiqueta selecionada
      for (let taskTag of taskTagData) {
        // Faça uma solicitação AJAX para obter os detalhes da tarefa
        $.post('/plannerfocus/MVC/Controller/taskController.class.php?command=getOne', { id_task: taskTag.id_task }, function (taskData) {
          let task = JSON.parse(taskData);
          templateTask(task);
          deleteTask();
          updateTask();
        });
      }
    });
  }
});


});

function formattedDate(date) {
const parsedDate = new Date(date);

if (isNaN(parsedDate.getTime())) {
  return '';
}

// Use os métodos Date para obter o ano, mês e dia e formate manualmente a data
const year = parsedDate.getFullYear();
const month = parsedDate.toLocaleString('pt-BR', { month: 'long' });
const day = parsedDate.getDate()+1;

// Crie uma string formatada no formato desejado
return `${day} de ${month} de ${year}`;
}

// Load Tags carrega as tags no select
TAGS = '';
function loadTags() {
$.get('/plannerfocus/MVC/Controller/tagController.class.php?command=getAll', function (data) {
  let tagData = JSON.parse(data);
  let selectTag = document.getElementById('selectTag');

  selectTag.innerHTML = '';
  selectTag.appendChild(new Option('Selecionar Etiqueta', ''));

  TAGS += '<option selected>Tarefas</option>';
    
  for (let tag of tagData) {
    selectTag.appendChild(new Option(tag.title, tag.id_tag));
    TAGS +=`<option value="${tag.id_tag}">${tag.title}</option>`;
  }
}).fail(function (http) {
  if (http.status != 200) {
    document.location = '/';
  }
});
};

// Load TaksTags carrega as tags associadas as tasks
function loadTasksTags(idTask) {
$.post('/plannerfocus/MVC/Controller/taskTagController.class.php?command=getAll', { id_task: idTask }, function (data) {
  let tagData = JSON.parse(data);
  let tagsContainer = $("#tags" + idTask);

  tagsContainer.empty(); // Limpa qualquer conteúdo anterior

  for (let tag of tagData) {
    $.post('/plannerfocus/MVC/Controller/tagController.class.php?command=getOne', { id_tag: tag.id_tag }, function (response) {
      let tagData = JSON.parse(response);

      let newTag = $('<span class="badge rounded-pill bg-white border border-dark text-dark">' +
      '<i class="bi bi-tag-fill" style="color:' + tagData.color + '; margin-right: 2px;"></i>' +
      tagData.title + 
      '<i class="bi bi-x-lg deleteTaskTag" data-id_task="' + tag.id_task + '"; data-id_tag="' + tag.id_tag + '"; style="margin-left: 5px;"></i>' +
      '</span>');
      newTag.addClass(`pill${tag.id_task}-${tag.id_tag}`);

      tagsContainer.append(newTag);
      deleteTaskTag();
  });
};
});
}

function insertTaskTag() {
$('.tasks_tags').off().on('change', function(){
  let idTask = $(this).data('id_task');
  let idTag = $(this).val();
  let tagsContainer = $("#tags" + idTask);

  $.post('/plannerfocus/MVC/Controller/tagController.class.php?command=getOne', { id_tag: idTag }, function (data) {
    let tagData = JSON.parse(data);
    let newTag = $('<span class="badge rounded-pill bg-white border border-dark text-dark">' +
    '<i class="bi bi-tag-fill" style="color:' + tagData.color + '; margin-right: 2px;"></i>' +
    tagData.title + 
    '<i class="bi bi-x-lg deleteTaskTag" data-id_task="' + idTask + '"; data-id_tag="' + idTag + '"; style="margin-left: 5px;"></i>' +
    '</span>');
    newTag.addClass(`pill${idTask}-${idTag}`);

    tagsContainer.append(newTag);
    
    $.post('/plannerfocus/MVC/Controller/taskTagController.class.php?command=insert', { id_task: idTask, id_tag: idTag });
    deleteTaskTag();
  });


});
};

function deleteTaskTag() {

$(".deleteTaskTag").off().on('click', function (e) {
    e.preventDefault(); // Evita comportamento padrão  
    let taskId = $(this).data('id_task');
    let tagId = $(this).data('id_tag');
    
    let dataToSend = {
      id_task: taskId,
      id_tag: tagId
    };

    // Post recebe dois parâmetros, uma URL e um objeto (dado) que enviará, também pode fazer um comportamento ao enviar
    $.post('/plannerfocus/MVC/Controller/taskTagController.class.php?command=delete', dataToSend, function (data) {
      $(`.pill${taskId}-${tagId}`).hide("fast");
    });
  
    return false; // Evita comportamento padrão
});

};

function templateTask(task) {
let taskList = $("#list");

let taskDiv = $("<div>").addClass("row justify-content-center align-items-center");
taskDiv.addClass(`line${task.id_task}`);
taskDiv.css("border-radius", "10px");

const displayDescription = task.description.length > 80 ? task.description.substring(0, 80) + "..." : task.description;
const displayDate = formattedDate(task.date);
const displayTime = task.time === "00:00:00" ? " " : task.time.slice(0, 5);

// Template para uma tarefa
let taskTemplate = `
  <div class="row">
    <div class="col-8">
      <i class="bi bi-app delete" data-id_task="${task.id_task}" style="font-size: 20px;"></i>
      <span style="font-size: 18px; vertical-align: 2px">${task.title}</span>
    </div>
    <div class="col-1">
      <i class="bi bi-pen update" data-id_task="${task.id_task}" style="font-size: 20px; margin-right: 10px;"></i>
    </div>
    <div class="col-2">
      <select class="form-select form-select-sm multiple tasks_tags" 
      data-id_task="${task.id_task}" aria-label="Tarefas" style="max-width: 70%">
      ${TAGS}
      </select>
    </div>
    <div class="row no-select">
      <div class="col-8">
        <span style="font-size: 12px; padding-left: 30px">${displayDescription}</span>
      </div>
      <div class="col-4">
        <span style="font-size: 12px; padding-left: 30px">${displayDate}</span>
        <span style="font-size: 12px; padding-left: 30px">${displayTime}</span>
      </div>
    </div>
    <div class="row no-select">
      <div class="col-8" id="tags${task.id_task}">
      </div>
    </div>
  </div>
`;


// Adicionar o template à div da tarefa
taskDiv.append(taskTemplate);
taskList.append(taskDiv);

insertTaskTag();
loadTasksTags(task.id_task);

// Divisória entre as tasks
let hr = $("<hr>").css({
    "margin-top": "2px",   // Define a margem superior
    "margin-bottom": "2px", // Define a margem inferior
    "border-color": "rgba(0, 0, 0, 0.6)"
});
hr.addClass(`line${task.id_task}`);
// Adicionar divisória
taskList.append(hr);
};

function templateTaskEdit(taskId) {

  if ($("#editTaskForm").length > 0) {
    alert('Já existe um formulário de edição aberto.');
    return;
  }

  let taskDiv = $("#list").find(`[data-id_task="${taskId}"]`);

  // Encontre a linha (row) que contém a div do updateTask
  let updateTaskRow = taskDiv.closest('.row:not(.no-select)');

  // Verifique se a div do updateTask e a linha foram encontradas
  if (taskDiv.length === 0 || updateTaskRow.length === 0) {
    alert('Tarefa ou linha não encontrada.');
    return;
  }

  // Crie um formulário de edição
  let editForm = `
  <form id="editTaskForm" class="text-center" data-id_task="${taskId}">
      <div class="row align-items-center no-select">
          <div class="col-4">
              <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-clipboard"></i></span>
                  <!--Campo do Formulário-->
                  <input type="text" id="editTitle" name="editTitle" class="form-control form-control-lg fs-6" placeholder="Dê um título para sua Tarefa" maxlength="30" required>
              </div>
          </div>
          <div style="flex: 0 0 30%; max-width: 30%;">
              <div class="input-group">
                  <!-- Campo do Formulário para Data -->
                  <input type="date" id="editDate" name="editDate" class="form-control form-control-lg fs-6">
                  <span class="input-group-text text-muted">Data <small>*Opcional</small></span>
              </div>
          </div>
          <div class="col-3">
              <div class="input-group">
                  <!-- Campo do Formulário para Horário -->
                  <input type="time" id="editTime" name="editTime" class="form-control form-control-lg fs-6">
                  <span class="input-group-text text-muted">Horário <small>*Opcional</small></span>
              </div>
          </div>
      </div>
      <br>
      <div class="row align-items-center no-select">
          <div class="col-8">
              <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-type"></i></span>
                  <!--Campo do Formulário-->
                  <textarea rows="2" id="editDescription" name="editDescription" class="form-control form-control-lg fs-6" placeholder="Dê um descrição para sua Tarefa *Opcional" maxlength="300"></textarea>
              </div>
          </div>
          <div class="col-3">
              <button type="submit" id="editInsertTaskButton" class="btn btn-lg btn-primary btn-gradient fs-6">Adicionar Tarefa <i class="bi bi-plus-lg"></i></button>
          </div>
      </div>
  </form>
  `;

  // Insira o formulário de edição abaixo da div da tarefa
  updateTaskRow.after(editForm);

  // Manipule o evento de envio do formulário
  $("#editTaskForm").submit(function (e) {
    e.preventDefault();
    
    // Obtenha os novos dados do formulário
    let taskId = $(this).data('id_task');
    let newTitle = $("#editTitle").val();
    let newDate = $("#editDate").val();
    let newTime = $("#editTime").val();
    let newDescription = $("#editDescription").val();

    // Verifique se o campo de título não está vazio
    if (newTitle.trim() === '') {
      alert('O campo de título não pode estar vazio.');
      return;
    }

    let currentDate = new Date();
    currentDate.setDate(currentDate.getDate() - 1);
    let selectedDateTime = new Date(newDate + ' ' + newTime);
    
    if (selectedDateTime < currentDate) {
      alert('Você não pode criar tarefas com datas no passado.');
      return;
    }


    if (newDate.trim() === '' && newTime.trim() !== '') {
      alert('Você deve preencher a data antes de incluir o horário.');
      return;
    }

    // Crie um objeto com os dados a serem enviados
    let formData = {
      id_task: taskId,
      title: newTitle,
      date: newDate,
      time: newTime,
      description: newDescription
    };
  
    // Envie os dados para o controlador PHP usando $.post ou $.ajax
    $.post('/plannerfocus/MVC/Controller/taskController.class.php?command=update', formData, function (data) {
      $("#list").empty();
      loadTask();
  
      // Remova o formulário de edição após a atualização bem-sucedida
      $("#editTaskForm").remove();
    });
  });
}

function loadTask() {
let response = $.get('/plannerfocus/MVC/Controller/taskController.class.php?command=getAll', function (data) {
    let taskData = JSON.parse(data);
    
    for (let task of taskData) {
        templateTask(task);
    }
    // Carregar função de deletar
    deleteTask();
    updateTask();
});

response.fail(function(http){
  if (http.status != 200){
    document.location = '/index.html';
  }
});
};

function insertTask() {
// Executa o comportamento ao clicar no botão
$("#insertTaskButton").on('click', function (e) {
    e.preventDefault(); // Evita comportamento padrão

    // Obtenha o valor do campo de título
    let titleValue = $("#title").val();
    let dateValue = $("#date").val();
    let timeValue = $("#time").val();

    // Verifique se o campo de título não está vazio
    if (titleValue.trim() === '') {
      alert('O campo de título não pode estar vazio.');
      return;
    }

    let currentDate = new Date();
    currentDate.setDate(currentDate.getDate() - 1);
    let selectedDateTime = new Date(dateValue + ' ' + timeValue);
    
    if (selectedDateTime < currentDate) {
      alert('Você não pode criar tarefas com datas no passado.');
      return;
    }


    if (dateValue.trim() === '' && timeValue.trim() !== '') {
      alert('Você deve preencher a data antes de incluir o horário.');
      return;
    }

    let task_dataForm = $("#taskForm").serialize(); // Pegar os dados do formulário
    
    // Post recebe dois parâmetros, uma URL e um objeto (dado) que enviará, também pode fazer um comportamento ao enviar
    $.post('/plannerfocus/MVC/Controller/taskController.class.php?command=insert', task_dataForm, function (data) {
        let task = JSON.parse(data);
        templateTask(task);
        deleteTask();
                 
    });

    return false; // Evita comportamento padrão
});
};

function updateTask() {
$(".update").off().on('click', function (e) {
  e.preventDefault(); // Evita comportamento padrão  
  let taskId = $(this).data('id_task'); // Pega o id_task da etiquta a ser deletada do atributo de dados

  // Verifique se a div da tarefa foi encontrada
  if (taskId.length === 0) {
    alert('Tarefa não encontrada!');
    return;
  }else{
    templateTaskEdit(taskId);
  }

  return false;
});
}

function deleteTask() {
// Executa o comportamento ao clicar no botão
$(".delete").off().on('click', function (e) {
    e.preventDefault(); // Evita comportamento padrão  
    let taskId = $(this).data('id_task'); // Pega o id_task da etiquta a ser deletada do atributo de dados
    
    let dataToSend = {
      id_task: taskId
    };

    // Post recebe dois parâmetros, uma URL e um objeto (dado) que enviará, também pode fazer um comportamento ao enviar
    $.post('/plannerfocus/MVC/Controller/taskController.class.php?command=delete', dataToSend, function (data) {
      $(`.line${taskId}`).hide("fast");
      $(`#editTaskForm[data-id_task="${taskId}"]`).remove();
    });
  
    return false; // Evita comportamento padrão
});
};
