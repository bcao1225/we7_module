function tableToExcel(json) {
    //列标题
    let str = `<tr>
                <th>姓名</th>
                <th>电话</th>
                <th>微信号</th>
                <th>打款金额</th>
                <th>用途</th>
                <th>打款时间</th>
               </tr>`;
    //循环遍历，每行加入tr标签，每个单元格加td标签
    json.forEach(item => {
            if(item.status==='1'){
                str += '<tr>';
                str += `<td>${item.name}</td>`;
                str += `<td>${item.tel}</td>`;
                str += `<td>${item.wx}</td>`;
                str += `<td>${item.price}</td>`;
                str += `<td>${item.text}</td>`;
                str += `<td>${item.acarttime}</td>`;
                str += '</tr>';
            }
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
    oA.download = `线下打款名单.xls`;
    // 模拟点击
    oA.click();
}

function formatZero(num, len) {
    if (String(num).length > len) return num;
    return (Array(len).join(0) + num).slice(-len);
}