import React from 'react';
import { BackHandler,SafeAreaView, StyleSheet,Text, TextInput, View, ScrollView,Platform } from "react-native";
import { useFocusEffect } from '@react-navigation/native';
import Button from './button';
import './global.js';

export default function Signup(props) {
    const styles = StyleSheet.create(
        Platform.select({
            ios:global.mainstyles,
            android:global.mainstyles,
            default:global.mainstyles
        }));
    
    const [email,updateEmail] = React.useState();
    const [pass,updatePass] = React.useState();
    const [emailver,updateEmailVer] = React.useState();
    const [passver,updatePassVer] = React.useState();
    const [name,setName] = React.useState();
    const [isLoading,setLoading] = React.useState(false);
    
    const emailVerRef = React.useRef(null);
    const passwordRef = React.useRef(null);
    const passwordVerRef = React.useRef(null);
    const nameRef = React.useRef(null);
    

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

    const signup = () => {
        setLoading(true);
        const requestOptions = {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                email: email,
                pass: pass,
                name: name
            }),
        };
        
        fetch(global.server + "user_account/create.php", requestOptions)
        .then(res =>res.json())
        .then(result =>{
            if(result.result)
            {
                alert("Thank you for signing up!");
                // Sign in afterwards.
                props.pagging(0);
            }else{
                alert("An error occured when signing up.");
            }
        }).catch(error => {
            console.log(error);
            alert("An error occured when signing up.");
        }).finally(() => {
            setLoading(false);
        });
    }



    return (
        <SafeAreaView>
        <ScrollView>
            <View>
            <Text
                style={styles.header}
            >Sign Up</Text>
            </View>
            <View style={styles.signin}>
                <Text>Email</Text>
                <TextInput
                    style={styles.input}
                    onChangeText={updateEmail}
                    value={email}
                    placeholder="John@Doe.com"
                    onSubmitEditing={() => {
                        emailVerRef.current.focus();
                    }}
                />
                <Text
                    style={((email === emailver)?(styles.good):(styles.bad))} 
                >Email Verification</Text>
                <TextInput
                    style={styles.input}
                    onChangeText={updateEmailVer}
                    value={emailver}
                    placeholder="John@Doe.com"
                    onSubmitEditing={() => {
                        passwordRef.current.focus();
                    }}
                    ref={emailVerRef}
                />
                <Text>Password</Text>
                <TextInput 
                    style={styles.input}
                    onChangeText={updatePass}
                    value={pass}
                    placeholder="*********"
                    secureTextEntry={true}
                    onSubmitEditing={() => {
                        passwordVerRef.current.focus();
                    }}
                    ref={passwordRef}
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
                    onSubmitEditing={() => {
                        nameRef.current.focus();
                    }}
                    ref={passwordVerRef}
                />
                <Text>Your Name</Text>
                <TextInput
                    style={styles.input}
                    onChangeText={setName}
                    value={name}
                    placeholder="John Doe"
                    onSubmitEditing={() => {
                        signup();
                    }}
                    ref={nameRef}
                />

                <Button 
                    textstyle={styles.leftbutton}
                    title="Sign Up"
                    onPress={() => {signup()}}
                    isLoading={isLoading}
                />
                <View style={styles.signup}>
                    <View style={styles.leftbuttonview}>
                        <Button
                            textstyle={styles.leftbutton}
                            title="Sign In"
                            onPress={() => {props.pagging(0)}}
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
            </ScrollView>
        </SafeAreaView>
    );
    
}