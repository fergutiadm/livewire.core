window.printBarcode = function () {
    const area = document.getElementById('print-area');
    if (!area) return;

    const content = area.innerHTML;

    const win = window.open('', '', 'width=400,height=600');

    win.document.write('<html><head><title>Imprimir</title></head><body>');
    win.document.write(content);
    win.document.write('</body></html>');

    win.document.close();
    win.print();
};
