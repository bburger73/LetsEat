import React, { useEffect } from 'react';
import { BackHandler,SafeAreaView, StyleSheet,Text, TextInput, View,Platform } from "react-native";
import { useFocusEffect } from '@react-navigation/native';
import Button from './button';
import './global.js';

export default function Forgot(props) {
    const [pass,updatePass] = React.useState();
    const [passver,updatePassVer] = React.useState();
    const [token,updateToken] = React.useState();
    const [isLoading,setLoading] = React.useState(false);
    const [resp,setResponse] = React.useState(false);

    useEffect(() =>{
        updateToken(props.data.token);
        // console.log(token);
    }, [updateToken]);

    useFocusEffect(
        React.useCallback(() => {
          const onBackPress = () => {
            props.pagging(0);
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

    const styles = StyleSheet.create(
        Platform.select({
            ios:global.mainstyles,
            android:global.mainstyles,
            default:global.mainstyles
        })
    );

    
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
    const storeStringData = async (key,value) => {
        try {
            await AsyncStorage.setItem(key, value)
        } catch (e) {}    
    }


    const recover = () => {
        if(pass !== "" && pass !== null && pass !== undefined)
        {
            let respx;
            setLoading(true);
            const requestOptions = {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    pass:pass,
                    token:token
                }),
            };
            console.log(requestOptions);
            fetch(global.server + 'user_forgot/update.php',requestOptions)
            .then(res => res.json())
            .then(response => {
                setResponse(response);
                respx = response;
            }).catch(() => {
                alert('Password failed to update. Cannot connect to server');
            }).finally(() => {
                // console.log(respx);
                if(respx.result)
                {
                    alert('Password has been updated!');
                    //This should sign you in.
                    updatePushToken(respx.user_token,props.expotoken);
                    storeStringData("projectlogintoken",respx.user_token);
                    props.signin(respx);
                    setLoading(false);
                    props.pagging(1);
                }else{
                    alert('Password failed to update.');
                    setLoading(false);
                }
            });
        }else{
            alert("Please fill in the email box.")
        }
    }


    return (
        <SafeAreaView>
            <Text
                style={styles.header}
            >Recover</Text>
            <Text>{"test: " + JSON.stringify(props.data)}</Text>
            <View style={styles.signin}>
                
                <Text>Password</Text>
                    <TextInput 
                        style={styles.input}
                        onChangeText={updatePass}
                        value={pass}
                        placeholder="*********"
                        secureTextEntry={true}
                    />
                    <Text
                        style={((pass === passver)?(styles.good):(styles.bad))} 
                    >Password Verification</Text>
                    <TextInput 
                        style={styles.input}
                        onChangeText={updatePassVer}
                        value={passver}
                        placeholder="*********"
                        secureTextEntry={true}
                    />
                <Button
                    textstyle={styles.topbutton}
                    title="Forgot"
                    onPress={() => {recover()}}
                    isLoading={isLoading}
                />
            </View>
            
        </SafeAreaView>
    );
    
}