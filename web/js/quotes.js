$('textarea').gre({
    content_css_url: '/js/plugins/gre.css',
    height: 250
});
$("#quote-item_id").select2();
$("#change_author").select2();
$(document).on('change', '#change_author', function () {
  var val = $(this).val();
  console.log('val', val);
  if (val != 0) {
    document.location.replace('https://app.harekrishna.ru/site/quotes?item_id='+val)
  } else {
    document.location.replace('https://app.harekrishna.ru/site/quotes');
  }
})
$(".image-uploader").imageUploader();

$("#data-table").DataTable({
    order: [[0, 'desc']],
    "language":
    {
        "processing": "Подождите...",
        "search": "Поиск:",
        "lengthMenu": "Показать _MENU_ записей",
        "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
        "infoEmpty": "Записи с 0 до 0 из 0 записей",
        "infoFiltered": "(отфильтровано из _MAX_ записей)",
        "infoPostFix": "",
        "loadingRecords": "Загрузка записей...",
        "zeroRecords": "Записи отсутствуют.",
        "emptyTable": "В таблице отсутствуют данные",
        "paginate": {
          "first": "Первая",
          "previous": "Предыдущая",
          "next": "Следующая",
          "last": "Последняя"
        },
        "aria": {
          "sortAscending": ": активировать для сортировки столбца по возрастанию",
          "sortDescending": ": активировать для сортировки столбца по убыванию"
        }
      }
});