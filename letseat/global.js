
//global.server = "https://dev.letseat.burgertechnologies.com/api-letseat/";
global.server = "http://192.168.0.141/Automation/local/letseat.com/api-letseat/"
global.styles = {
    main:"#000000",
    accent:"#6D6D6D",
    background:"#ffffff"
}


global.mainstyles = {
    
    datePickerStyle: {
        width: 200,
        marginTop: 20,
    },
    navbar:{
        flexDirection: 'row',
        justifyContent: 'space-between',
        height: 60,
        backgroundColor:global.styles.background,
    },backbuttonview:{
        position: 'absolute',
        left: 20,
        backgroundColor:global.styles.background,
        color: global.styles.main,
        top: 15,
        textAlign: "center",
    },backbutton:{
        color:global.styles.main,
        backgroundColor:global.styles.background,
        margin:5,
        fontSize:20
    },addbuttonview:{
        position: 'absolute',
        right: 20,
        backgroundColor:global.styles.background,
        color: global.styles.main,
        top: 15,
        textAlign: "center",
    },addbutton:{
        color:global.styles.main,
        backgroundColor:global.styles.background,
        margin:5,
        fontSize:20
    },header: {
        marginTop:10,
        textAlign:"center",
        color:global.styles.main ,
        fontWeight: 'bold',
        fontSize: 30,
    },signin:{
        margin:20,
        // width:"100%"
    },input: {
        height: 50,
        margin: 12,
        borderWidth: 1,
        padding: 10,
        color: global.styles.main,
        backgroundColor: global.styles.background
    },signup:{
        flexDirection: 'row',
    },leftbuttonview:{
        width:"50%",
    },rightbuttonview:{
        width:"50%",
    },topbutton:{
        alignSelf: "stretch",
        alignSelf: 'stretch',
        textAlign: 'center',
        color:global.styles.background,
        backgroundColor:global.styles.main,
        margin:5,
        fontSize:20,
        padding:5,
    },leftbutton:{
        alignSelf: "stretch",
        alignSelf: 'stretch',
        textAlign: 'center',
        color:global.styles.background,
        backgroundColor:global.styles.main,
        fontSize:20,
        margin:5,
        padding:5,
    },rightbutton:{
        alignSelf: "stretch",
        alignSelf: 'stretch',
        textAlign: 'center',
        color:global.styles.background,
        backgroundColor:global.styles.main,
        fontSize:20,
        margin:5,
        padding:5,
    },dashboardheader:{
        fontSize:30,
        textAlign:"center"
    },settingsbutton:{
        color:global.styles.main,
        backgroundColor:global.styles.background,
        textAlign:'center',
        fontSize:20,
        margin:3,
        padding:5,
    },scrollView:{
        // flexDirection: 'row',
        // justifyContent: 'space-between',
        height: 'auto',
        // width:"100%",
        // maxHeight:"95%"
        marginBottom:60
    },main:{
        maxHeight:"100%",
    }
}

global.settings = {
    navbar:{
        flexDirection: 'row',
        justifyContent: 'space-between',
        height: 60,
        backgroundColor:global.styles.background,
    },backbuttonview:{
        position: 'absolute',
        left: 20,
        backgroundColor:global.styles.background,
        color: global.styles.main,
        top: 15,
        textAlign: "center",
    },backbutton:{
        color:global.styles.main,
        backgroundColor:global.styles.background,
        margin:5,
        fontSize:20
    },
    signin:{
        margin:20
    },
    input: {
        height: 50,
        margin: 12,
        borderWidth: 1,
        padding: 10,
        color: global.styles.main,
        backgroundColor: global.styles.background
    },
    button:{
        height: 50,
        margin: 12,
        borderWidth: 1,
        padding: 10,
        color: global.styles.color,
        backgroundColor: global.styles.background
    },
    good:{
        color:global.styles.background
    },bad:{
        color:"red",
    },scrollView:{
        maxHeight:"100%"
    },main:{
        maxHeight:"100%"
    }
}