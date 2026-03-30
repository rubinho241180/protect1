// WAPP.getMyLabels = async function() {

//     // LABELS
//     const allLabels = await WPP.labels.getAllLabels();
//     const modLabels = allLabels.map(({ color, colorIndex, ...resto }) => resto);
//     console.log(JSON.stringify({href: "/labels", data: modLabels}));    
// }

WAPP.getMyLabels = async function() {
    // LABELS
    const allLabels = await WPP.labels.getAllLabels();
    const modLabels = allLabels.map(({ color, colorIndex, ...resto }) => {
        return {
            ...resto,
            color: resto.hexColor
        };
    });
    
    // Remove hexColor property and keep only color
    const finalLabels = modLabels.map(({ hexColor, ...label }) => label);
    
    console.log(JSON.stringify({href: "/labels", data: finalLabels}));    
}



// await WPP.chat.list({withLabels: ['15']})


// Função para monitorar todos os eventos de labels
WAPP.monitoringLabels = function() {
    const labelStore = WPP.whatsapp.LabelStore;
    
    // Monitorar adição de labels
    labelStore.off('add').on('add', async function(label) {
        let data = await WPP.labels.getLabelById(label.id);
        console.log(JSON.stringify({
            href: "/labels/insert",
            data: {
                id: label.id,
                name: label.name,
                color: data.hexColor,
                count: label.count
            }
        }));
    });
    
    // Monitorar remoção de labels
    labelStore.off('remove').on('remove', async function(label) {
        console.log(JSON.stringify({
            href: "/labels/delete",
            data: {
                id: label.id,
                name: label.name
            }
        }));
    });
    
    // Monitorar alterações em labels
    labelStore.off('change').on('change', async function(label) {
        let data = await WPP.labels.getLabelById(label.id);
        console.log(JSON.stringify({
            href: "/labels/update",
            data: {
                id: label.id,
                name: label.name,
                color: data.hexColor,
                count: label.count
            } 
        }));
    });

    WPP.on("chat.update_label", async (chat) => {
        console.log('chat.update_label:', chat);

        console.log(JSON.stringify({
            href: "/contact/update:labels",
            data: {
                id: label.id,
                name: label.name,
                color: data.hexColor,
                count: label.count
            }
        }));     
});

    
    console.log('monitoringLabels: ON - Monitoring all label events;');
}

// Iniciar o monitoramento após um pequeno atraso para garantir que o WPP esteja carregado
// setTimeout(() => {
//     try {
//         WAPP.monitoringLabels();
//     } catch (error) {
//         console.error('Error starting label monitoring:', error);
//     }
// }, 2500);

console.log('---=> 20_labels.js');