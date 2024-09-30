// Aguarde o documento HTML estar totalmente carregado
$(document).ready(function(){
    loadTag();
    insertTag();
});


// Função para criar um template de uma etiqueta
function templateTag(tag) {
  let tagList = $("#list");

  let tagDiv = $("<div>").addClass("row justify-content-center align-items-center");
  tagDiv.addClass(`line${tag.id_tag}`);
  tagDiv.css("border-radius", "10px");

  // Template para uma etiqueta
  let tagTemplate = `
      <div class="col-9">
        <i class="bi bi-tag-fill" style="color: ${tag.color}; font-size: 24px; margin-right: 20px;"></i>
        <span style="font-size: 18px; vertical-align: 2px">${tag.title}</span>
      </div>
      <div class="col-2">
        <i class="bi bi-pen update" data-id_tag="${tag.id_tag}" style="font-size: 20px; margin-right: 10px;"></i>
        <i class="bi bi-trash3 delete" data-id_tag="${tag.id_tag}" style="font-size: 20px;"></i>
      </div>
  `;

  // Adicionar o template à div da etiqueta
  tagDiv.append(tagTemplate);
  tagList.append(tagDiv);

  // Divisória entre as tags
  let hr = $("<hr>").css({
      "margin-top": "2px",   // Define a margem superior
      "margin-bottom": "2px", // Define a margem inferior
      "border-color": "rgba(0, 0, 0, 0.6)"
  });
  hr.addClass(`line${tag.id_tag}`);
  // Adicionar divisória
  tagList.append(hr);
};

// Função para criar um template para editar uma etiqueta
function templateTagEdit(tagId) {

    if ($("#editTagForm").length > 0) {
      alert('Já existe um formulário de edição aberto.');
      return;
    }

    let tagDiv = $("#list").find(`[data-id_tag="${tagId}"]`);

    // Encontre a linha (row) que contém a div do updateTag
    let updateTagRow = tagDiv.closest('.row');

    // Verifique se a div do updateTag e a linha foram encontradas
    if (tagDiv.length === 0 || updateTagRow.length === 0) {
      alert('Etiqueta ou linha não encontrada.');
      return;
    }

    // Crie um formulário de edição
    let editForm = `
    <form id="editTagForm" data-id_tag="${tagId}">
      <div class="row justify-content-between align-items-center">
        <div class="col-5">
          <div class="input-group" style="margin-left:10%">
            <span class="input-group-text"><i class="bi bi-tag"></i></span>
            <input type="text" id="editTagTitle" name="title" class="form-control form-control-lg fs-6" placeholder="Dê um título para sua Etiqueta" maxlength="30" style="max-width: 80% ">
          </div>
        </div>
        <div class="col-4">
          <div class="input-group">
            <input type="color" id="editTagColor" name="color" class="form-control form-control-color fs-6" value="#3493fa" required style="max-width: 40px;">
            <span class="input-group-text text-muted">Dê uma cor para a Etiqueta</span>
          </div>
        </div>
        <div class="col text-center">
          <button type="submit" id="insertTagButton" class="btn btn-lg btn-primary btn-gradient fs-6">Editar Etiqueta <i class="bi bi-pen-fill"></i></i></button>
        </div>
      </div>
    </form>
    `;

    // Insira o formulário de edição abaixo da div da etiqueta
    updateTagRow.after(editForm);

    // Manipule o evento de envio do formulário
    $("#editTagForm").submit(function (e) {
      e.preventDefault();
      
      // Obtenha os novos dados do formulário
      let tagId = $(this).data('id_tag');
      let newTitle = $("#editTagTitle").val();
      let newColor = $("#editTagColor").val();

      // Verifique se o campo de título não está vazio
      if (newTitle.trim() === '') {
        alert('O campo de título não pode estar vazio.');
        return;
      }

      // Crie um objeto com os dados a serem enviados
      let formData = {
        id_tag: tagId,
        title: newTitle,
        color: newColor
      };
    
      // Envie os dados para o controlador PHP usando $.post ou $.ajax
      $.post('/plannerfocus/MVC/Controller/tagController.class.php?command=update', formData, function (data) {
        $("#list").empty();
        loadTag();
    
        // Remova o formulário de edição após a atualização bem-sucedida
        $("#editTagForm").remove();
      });
    });
}

// Função para ler todas as tags
function loadTag() {
  
  let response = $.get('/plannerfocus/MVC/Controller/tagController.class.php?command=getAll', function (data) {
      let tagData = JSON.parse(data);
      
      for (let tag of tagData) {
          templateTag(tag);
      }
      // Carregar função de deletar
      deleteTag();
      updateTag();
  });

  response.fail(function(http){
    if (http.status != 200){
      document.location = '/index.html';
    }
  });
};

// Função para inserir uma nova tag
function insertTag() {
  // Executa o comportamento ao clicar no botão
  $("#insertTagButton").on('click', function (e) {
      e.preventDefault(); // Evita comportamento padrão

      // Obtenha o valor do campo de título
      let titleValue = $("#title").val();

      // Verifique se o campo de título não está vazio
      if (titleValue.trim() === '') {
        alert('O campo de título não pode estar vazio.');
        return;
      }

      let tag_dataForm = $("#tagForm").serialize(); // Pegar os dados do formulário

      // Post recebe dois parâmetros, uma URL e um objeto (dado) que enviará, também pode fazer um comportamento ao enviar
      $.post('/plannerfocus/MVC/Controller/tagController.class.php?command=insert', tag_dataForm, function (data) {
          let tag = JSON.parse(data);
          templateTag(tag);
          deleteTag();
          updateTag();
      });

      return false; // Evita comportamento padrão
  });
};

function updateTag() {
  $(".update").off().on('click', function (e) {
    e.preventDefault(); // Evita comportamento padrão  
    let tagId = $(this).data('id_tag'); // Pega o id_tag da etiquta a ser deletada do atributo de dados

    // Verifique se a div da etiqueta foi encontrada
    if (tagId.length === 0) {
      alert('Etiqueta não encontrada!');
      return;
    }else{
      templateTagEdit(tagId);
    }

    return false;
  });
}  

// Função para inserir uma nova tag
function deleteTag() {
  // Executa o comportamento ao clicar no botão
  $(".delete").off().on('click', function (e) {
      e.preventDefault(); // Evita comportamento padrão  
      let tagId = $(this).data('id_tag'); // Pega o id_tag da etiquta a ser deletada do atributo de dados
      
      let dataToSend = {
        id_tag: tagId
      };

      // Post recebe dois parâmetros, uma URL e um objeto (dado) que enviará, também pode fazer um comportamento ao enviar
      $.post('/plannerfocus/MVC/Controller/tagController.class.php?command=delete', dataToSend, function (data) {
        $(`.line${tagId}`).hide("fast");
        $(`#editTagForm[data-id_tag="${tagId}"]`).remove();
      });
    
      return false; // Evita comportamento padrão
  });
};
