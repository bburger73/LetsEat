import React, { useEffect } from 'react';
import {Platform, BackHandler,SafeAreaView, StyleSheet,Text,View } from "react-native";
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useFocusEffect } from '@react-navigation/native';
import Button from './button';
import './global.js';

export default function UserDashboard(props) {

    useEffect(() =>{

    }, []);
    useFocusEffect(
        React.useCallback(() => {
          const onBackPress = () => {
            goback();
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
    
    const storeStringData = async (key,value) => {
        try {
            await AsyncStorage.setItem(key, value)
        } catch (e) {
            // saving error
        }    
    }


    const styles = StyleSheet.create(
        Platform.select({
            ios:global.mainstyles,
            android:global.mainstyles,
            default:global.mainstyles
        })
    );


    const goback = () => {
        storeStringData('projectkmsi',"false");
        storeStringData("projecttoken",'');
        storeStringData("projectpass",'');
        props.pagging(0)
    }

    return (
        <SafeAreaView>
            <View style={styles.navbar}>
                <View style={styles.backbuttonview}>
                    <Button 
                        textstyle={styles.backbutton}
                        title="Sign Out"
                        onPress={goback}>
                    </Button>
                </View>
                <View style={styles.addbuttonview}>
                    <Button
                        textstyle={styles.addbutton}
                        title="Settings"
                        onPress={() => props.pagging(5)}>
                    </Button>
                </View>
            </View>
            <Text style={styles.dashboardheader}>
                Welcome {props.user.name}!
            </Text>
            <Button
                title="Manage Restaurant"
                textstyle={styles.topbutton}
                onPress={() => props.pagging(18)}></Button>
            {/* <Button
                title="Create Poll"
                textstyle={styles.topbutton}
                onPress={() => props.pagging(7)}></Button> */}
            <Button
                title="Manage Restaurant Group"
                textstyle={styles.topbutton}
                onPress={() => props.pagging(10)}></Button>
            <Button
                title="Manage Feast Groups"
                textstyle={styles.topbutton}
                onPress={() => props.pagging(12)}></Button>
            <Button
                title="Manage Polls"
                textstyle={styles.topbutton}
                onPress={() => props.pagging(14)}></Button>
            <Button
                title="Manage Votes"
                textstyle={styles.topbutton}
                onPress={() => props.pagging(16)}></Button>
            <Button
                title="View Results"
                textstyle={styles.topbutton}
                onPress={() => props.pagging(20)}></Button>
        </SafeAreaView>
        
    )
}