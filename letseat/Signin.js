import React, { useEffect }  from 'react';
import { BackHandler, Switch, SafeAreaView, StyleSheet,Text, TextInput, View, Platform, Alert } from "react-native";
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useFocusEffect } from '@react-navigation/native';
import Button from './button';
import './global.js';


export default function Signin(props) {
    const styles = StyleSheet.create(
        Platform.select({
            ios:global.mainstyles,
            android:global.mainstyles,
            default:global.mainstyles
        })
    );
    const [email,updateEmail] = React.useState("");
    const [pass,updatePassVal] = React.useState("");
    const [result,updateResults] = React.useState({});
    const [token,setToken] = React.useState();
    const [isLoading,setLoading] = React.useState(false);
    const [signinData,setSignin] = React.useState({});
    // const [visible, setVisible] = React.useState(false);

    const [isEnabled, setIsEnabled] = React.useState();
    const passRef = React.useRef();
    const emailRef = React.useRef();

    useFocusEffect(
        React.useCallback(() => {
          const onBackPress = () => {
            Alert.alert(
                "Exit App",
                "Would you like to exit?",
                [
                    {
                      text: "Yes",
                      onPress: () => BackHandler.exitApp(),
                      style: "confirm",
                    },
                    {
                      text: "No",
                      style: "cancel",
                    },
                ],
                {
                  cancelable: true,
                  onDismiss: () =>{}
                });
            // (confirm('Would you like to exit?')?()=>handleExit():()=>handleStay())
            return true;
          };
       
          BackHandler.addEventListener(
            'hardwareBackPress', onBackPress
          );
       
          return () =>
            BackHandler.removeEventListener(
              'hardwareBackPress', onBackPress
            );
        }, [])
    );
    const toggleSwitch = () =>{
        let bol = !isEnabled;
        setIsEnabled(bol);
        storeStringData('projectkmsi',bol.toString());
    }

    const storeStringData = async (key,value) => {
        try {
            await AsyncStorage.setItem(key, value)
        } catch (e) {}    
    }

    const getStringData = async (key) => {
        try {
            const value = await AsyncStorage.getItem(key);
            if (value !== null) {
                return value;
            }else{
                return null;
            }
        } catch (e) {
            return null;
        }
    }

    // const updatePass = (event) => {
    //     updatePassVal(event.value);       
    //     if (event.code === "Enter" || event.code === "NumpadEnter") {
    //         signin();
    //         console.log("entered");
    //     }
    //     document.addEventListener("keydown", listener);
    // }

    useEffect(() =>{
        async function fetchData() {
            setLoading(true);
            let kmsi = await getStringData('projectkmsi');
            let token = await getStringData('projectlogintoken');
            //console.log(token);
            if(token !== undefined && token !== null && token !== "" && kmsi === "true"){
                await setToken(token);
                await setIsEnabled(kmsi === "true");
                // props.pagging(1);
                if(token !== undefined && token !== null && token !== ""){
                    const requestOptions = {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({
                            token:token
                        }),
                    };
                    let resultout;
                    fetch(global.server + "user_account/read.php",requestOptions) 
                    .then(res => res.json())
                    .then(results => {     
                        // setSignin(results);
                        resultout = results;
                    }).catch(function(error) {
                        throw error;
                    }).finally(() => {
                        setTimeout(() => {
                            setLoading(false);
                            if(parseInt(resultout.id) > 0 || resultout.auth > 0)
                            {
                                updateResults(resultout);
                                updatePushToken(resultout.user_token,props.token);
                                storeStringData("projectlogintoken",resultout.user_token);
                                setLoading(false);
                                props.signin(resultout);
                                props.pagging(1)
                            }else{
                                alert("Failed To Sign In With Keep Me Logged In");
                                setLoading(false);
                            }
                        },2000);
                    });
                }
            }else{
                storeStringData("projectlogintoken","");
                setLoading(false);
            }
        };
        fetchData();
        if(Platform.OS !== 'ios' && Platform.OS !== 'android')
        {
            const listener = event => {
                if (event.code === "Enter" || event.code === "NumpadEnter") {
                    event.preventDefault();
                    signin();
                }
            };
            document.addEventListener("keydown", listener);
            
            return () => {
                document.removeEventListener("keydown", listener);
            };
        }

    }, []);


    const updatePushToken = (user_token,token) => {
        console.log(token);
        if(token !== null && token !== "" && token !== undefined)
        {
            const requestOptions = {
                method: "PUT",
                headers: {
                    "LETSEAT-AUTHKEY": user_token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    token:token
                }),
                };
            
            fetch(global.server + "user_push/update.php",requestOptions).then(res => res.json()).then(result => {
                console.log(result);
            });
        }
    }

    const signin = () => {
        setLoading(true);
        const requestOptions = {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: ((Platform.OS !== 'ios' && Platform.OS !== 'android')?(
                JSON.stringify({
                    email:emailRef.current.value,
                    pass:passRef.current.value
            })):(
                JSON.stringify({
                    email:email,
                    pass:pass
                })
            ))
        };
        console.log(requestOptions)

        let resultout;
        fetch(global.server + "user_account/read.php",requestOptions)
        .then(res => res.json())
        .then(results => {
            console.log(results);
            // setSignin(results);
            resultout = results;
        }).catch(() => {
            setTimeout(() => {
                setLoading(false);
                alert("Failed To Sign In Error");
            },2000);
        }).finally(() => {
            setTimeout(() => {
                if(parseInt(resultout.id) > 0 || resultout.authority === 0)
                {
                    updateResults(resultout);
                    updatePushToken(resultout.user_token,props.token);
                    storeStringData("projectlogintoken",resultout.user_token);
                    props.signin(resultout);
                    setLoading(false);
                    props.pagging(1)
                }else{
                    alert("Failed To Sign In");
                    setLoading(false);
                }
            },2000);
        });
    }

    return (
        <SafeAreaView>
            <Text
                style={styles.header}
            >Sign In</Text>
            
            <View style={styles.signin}>
                <Text>Email</Text>
                <TextInput
                    style={styles.input}
                    onChangeText={updateEmail}
                    value={email}
                    placeholder="John@Doe.com"
                    onSubmitEditing={() => {
                        passRef.current.focus()
                    }}
                    ref={emailRef}
                />
                <Text>Password</Text>
                <TextInput 
                    style={styles.input}
                    onChangeText={updatePassVal}
                    value={pass}
                    placeholder="*********"
                    onSubmitEditing={() => {
                        signin();
                    }}
                    secureTextEntry={true}
                    ref={passRef}
                />
                <Text>Keep Me Logged In?</Text>

                <Switch
                    trackColor={{false: global.styles.accent, true: global.styles.main}}
                    thumbColor={!isEnabled ? global.styles.main:undefined}
                    activeThumbColor={global.styles.accent}
                    ios_backgroundcolor={"#000000"} 
                    onValueChange={toggleSwitch}
                    value={isEnabled}
                />
                <Button
                    textstyle={styles.topbutton}
                    title="Sign In"
                    onPress={() => {
                        signin();
                    }}
                    isLoading={isLoading}
                />
                <View style={styles.signup}>
                    <View style={styles.leftbuttonview}>
                        <Button
                            textstyle={styles.leftbutton}
                            title="Sign Up"
                            onPress={() => {props.pagging(3)}}
                        ></Button>
                    </View>
                    <View style={styles.rightbuttonview}>
                        <Button
                            textstyle={styles.rightbutton}
                            title="Forgot"
                            onPress={() => {props.pagging(4)}}
                        ></Button>
                    </View>
                </View>
            </View>
        </SafeAreaView>
    );
    
}