import React from 'react';
import { BackHandler,SafeAreaView, StyleSheet,Text, TextInput, View,Platform } from "react-native";
import { useFocusEffect } from '@react-navigation/native';
import Button from './button';
import './global.js';

export default function Forgot(props) {
    const [email,updateEmail] = React.useState();
    const [isLoading,setLoading] = React.useState(false);

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

    
    const recover = () => {
        if(email !== "" && email !== null && email !== undefined)
        {
            setLoading(true);
            const requestOptions = {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    email:email,
                }),
            };
            fetch(global.server + 'user_forgot/create.php',requestOptions)
            .then(res => res.json())
            .then(response => {
                //console.log(response);
                if(!response.create)
                {
                    alert('Email failed to be sent.');
                }else{
                    alert('Email has been sent!');
                }
                setLoading(false);
            });
        }else{
            alert("Please fill in the email box.")
        }
    }


    return (
        <SafeAreaView>
            <Text
                style={styles.header}
            >Forgot</Text>
            
            <View style={styles.signin}>
                <Text>Email</Text>
                <TextInput
                    style={styles.input}
                    onChangeText={updateEmail}
                    value={email}
                    placeholder="John@Doe.com"
                />
                <Button 
                    // onPress={() => {
                    //     recover();
                    // }}
                    // style={{
                    //     position:'absolute',
                    //     bottom:50,
                    //     right:0,
                    //     width:20
                    // }}
                    // title="Forgot"  
                    // color={global.styles.main}  
                    // accessibilityLabel="Sign In"
                    
                    textstyle={styles.topbutton}
                    title="Forgot"
                    onPress={() => {recover()}}
                    isLoading={isLoading}
                />
                <View style={styles.signup}>
                    <View style={styles.leftbuttonview}>
                        <Button 
                            // title="Sign In"
                            // color={global.styles.main}  
                            // onPress={() => {
                            //     props.pagging(0)
                            // }}
                            textstyle={styles.leftbutton}
                            title="Sign In"
                            onPress={() => {props.pagging(0)}}
                        ></Button>
                    </View>
                    <View style={styles.rightbuttonview}>
                        <Button 
                            // title="Sign Up"
                            // color={global.styles.main}  
                            // onPress={() => {
                            //     props.pagging(3)
                            // }}
                            textstyle={styles.rightbutton}
                            title= "Sign Up"
                            onPress={() => {props.pagging(3)}}
                        ></Button>
                    </View>
                </View>
            </View>
            
        </SafeAreaView>
    );
    
}