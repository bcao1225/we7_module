function tableToExcel(json) {
    //列标题
    let str = `<tr>
                <th>姓名</th>
                <th>电话</th>
                <th>年级</th>
                <th>专业</th>
                
                <th>单位</th>
                <th>职务</th>
                <th>订单号</th>
                
                <th>金额</th>
                <th>打款时间</th>
               </tr>`;
    //循环遍历，每行加入tr标签，每个单元格加td标签
    json.forEach(item => {

        str += '<tr>';
        str += `<td>${item.name}</td>`;
        str += `<td>${item.tel}</td>`;
        str += `<td>${item.grade}</td>`;
        str += `<td>${item.professional}</td>`;

        str += `<td>${item.unit}</td>`;
        str += `<td>${item.Identity}</td>`;
        str += `<td><span>&nbsp;</span>${item.ordernumlist}</td>`;

        str += `<td>${item.price}</td>`;
        str += `<td>${item.carttime}</td>`;
        str += '</tr>';

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
    oA.download = `线上打款名单.xls`;
    // 模拟点击
    oA.click();
}

function formatZero(num, len) {
    if (String(num).length > len) return num;
    return (Array(len).join(0) + num).slice(-len);
}