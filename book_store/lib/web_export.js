function tableToExcel(json, {guild_name, guild_id}) {
    //列标题
    let str = `<tr>
                <th>管藏地</th>
                <th>在馆状态</th>
                <th>书籍编号</th>
                <th>馆内剩余</th>
               </tr>`;
    //循环遍历，每行加入tr标签，每个单元格加td标签
    json.forEach(item => {
        item.books.forEach(book => {
            guild_id = formatZero(Number.parseInt(guild_id), 4) + '';
            let bookrack = formatZero(Number.parseInt(item.id), 3) + '';
            let book_id = formatZero(Number.parseInt(book.id), 3) + '';
            str += '<tr>';
            str += `<td>${guild_name}</td>`;
            str += `<td>${book.type ? book.type.name : '未定义'}</td>`;
            str += `<td>${guild_id + bookrack + book_id}</td>`;
            str += `<td>1</td>`;
            str += '</tr>';
        })
    });
    let excelHtml = `
        <html>
            <head>
             <meta charset='utf-8' />
            </head>
             <body>
                <table>
                   ${str}
                </table>
             </body>
        </html>`;

    let blob = new Blob([excelHtml], {type: 'application/vnd.ms-excel'});

    // 创建一个a标签
    let oA = document.createElement('a');

    // 利用URL.createObjectURL()方法为a元素生成blob URL
    let url = URL.createObjectURL(blob);
    oA.href = url;
    // 给文件命名
    oA.download = `${guild_name}.xls`;
    // 模拟点击
    oA.click();
}

function formatZero(num, len) {
    if (String(num).length > len) return num;
    return (Array(len).join(0) + num).slice(-len);
}