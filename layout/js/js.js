$(document).ready(function () {
  $(".add").click(function (e) {
    e.preventDefault();
    let form = this.closest("form");
    let action = form.action;
    let name = $(form).find('input[name="name"]').val();
    let quantity = $(form).find('input[name="quantity"]').val();
    let sku = $(form).find('input[name="sku"]').val();
    let price = $(form).find('input[name="price"]').val();

    let ajout = $.post(action, {
      url: action,
      name: name,
      quantity: quantity,
      price: price,
    });

    ajout.done(function () {
      $(".modal-footer > button").click();
      swal(
        "Le produit est ajouté au panier avec succes",
        `la quantité ajouté est ${quantity}`,
        "success"
      );

      $("#cart .modal-body tbody").append(`
        <tr>
            <td>${name}</td>
            <td>${quantity}</td>
            <td><a class="remove-from-cart" href="panier.php?delete=${sku}" ><i class="fa fa-trash" ></i></a></td>
        </tr>
      `);
    });
    ajout.fail(function () {
      swal("Oops", "Il ya une erreur", "error");
    });
  });

  //

  $(".remove-from-cart").click(function (e) {
    e.preventDefault();
    const action = $(this).attr("href");
    const that = $(this);
    let deleted = $.get(action, {
      url: action,
    });

    deleted.done(function () {
      that.closest("tr").remove();
      return false;
    });
  });

  $(".payer").click(function (e) {
    e.preventDefault();
    let form = this.closest("form");
    let action = form.action;

    let payement = $.post(action, {
      url: action,
    });

    payement.done(function () {
      $(".modal-footer > button").click();
      swal(
        "Votre commande a été prise en considération",
        "merci hh",
        "success"
      );
      $("#cart .modal-body tbody").remove();
    });
    ajout.fail(function () {
      swal("Oops", "Il ya une erreur", "error");
    });
  });
});
